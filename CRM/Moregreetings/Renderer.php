<?php
/*-------------------------------------------------------+
| SYSTOPIA - MORE GREETINGS EXTENSION                    |
| Copyright (C) 2017 SYSTOPIA                            |
| Author: B. Endres (endres@systopia.de)                 |
|         P. Batroff (batroff@systopia.de)               |
| http://www.systopia.de/                                |
+--------------------------------------------------------+
| This program is released as free software under the    |
| Affero GPL license. You can redistribute it and/or     |
| modify it under the terms of this license which you    |
| can read by viewing the included agpl.txt or online    |
| at www.gnu.org/licenses/agpl.html. Removal of this     |
| copyright header is strictly prohibited without        |
| written permission from the original author(s).        |
+--------------------------------------------------------*/

/**
 * update current greetings
 *
 */
class CRM_Moregreetings_Renderer {

  /**
   * Re-calculate the more-greetings for one contact
   */
  public static function updateMoreGreetings($contact_id, $contact = NULL) {
    // load the templates
    $templates = CRM_Core_BAO_Setting::getItem('moregreetings', 'moregreetings_templates');
    if (!is_array($templates)) {
      return NULL;
    }

    // load the contact
    if ($contact == NULL) {
      // remark: if you change these parameters, see if you also want to adjust
      //  CRM_Moregreetings_Job::run and CRM_Moregreetings_Renderer::updateMoreGreetingsForContacts
      $contact = civicrm_api3('Contact', 'getsingle', array(
        'id'     => $contact_id,
        'return' => self::getUsedContactFields($templates),
      ));
    }

    // TODO: assign more stuff?
    // prepare smarty
    $smarty = CRM_Core_Smarty::singleton();
    CRM_Utils_Smarty::registerCustomFunctions($smarty);
    $smarty->assign('contact', $contact);


    // get the fields to render
    $greetings_to_render = self::getGreetingsToRender($contact, $templates, $contact);

    // render the greetings
    $greetings_update = array();
    foreach ($greetings_to_render as $greeting_key => $template) {
      $new_value = $smarty->fetch("string:$template");
      $new_value = trim($new_value);
      // check if the value is really different (avoid unecessary updates)
      if ($new_value != $contact[$greeting_key]) {
        $greetings_update[$greeting_key] = $new_value;
      }
    }

    // finally: run the update if there are changes
    if (!empty($greetings_update)) {
      $greetings_update['entity_id'] = $contact_id;
      $greetings_update['entity_table'] = 'civicrm_contact';
      civicrm_api3('CustomValue', 'create', $greetings_update);
    } else {
      // error_log("Nothing to do");
    }
  }


  /**
   * Re-calculate the more-greetings for a list of contacts ()
   *
   * @param $from_id    only consider contact with ID >= $from_id
   * @param $max_count  process no more than $max_count contacts
   *
   * @return last contact ID processed, 0 if none
   */
  public static function updateMoreGreetingsForContacts($from_id, $max_count) {
    $templates = CRM_Core_BAO_Setting::getItem('moregreetings', 'moregreetings_templates');

    // remark: if you change these parameters, see if you also want to adjust
    //  CRM_Moregreetings_Job::run and CRM_Moregreetings_Renderer::updateMoreGreetings
    $query_paremeters = array(
      'id'         => array('>=' => $from_id),
      'is_deleted' => 0,
      'sequential' => 1,
      'return'     => self::getUsedContactFields($templates),
      'options'    => array('limit' => $max_count,
                            'sort'  => 'id asc'));

    $contact_query = civicrm_api3('Contact', 'get', $query_paremeters);

    $last_id = 0;
    foreach ($contact_query['values'] as $contact) {
      $last_id = $contact['id'];
      self::updateMoreGreetings($last_id, $contact);
    }

    return $last_id;
  }


  /**
   * Get an array [custom_key] => [template]
   * of the fields to be rendered for this contact,
   * i.e. all the fields are there and not protected
   */
  protected static function getGreetingsToRender($contact, $templates, $current_data) {
    // first: load
    $active_fields = CRM_Moregreetings_Config::getActiveFields();

    // compile a list of protected field data (field_numbers)
    $protected_fields = array();
    foreach ($active_fields as $field_id => $field) {
      if (preg_match("#^greeting_field_(?P<field_number>\\d+)_protected$#", $field['name'], $matches)) {
        $field_number = $matches['field_number'];
        if (!empty($current_data["custom_{$field['id']}"])) {
          $protected_fields[] = $field_number;
        }
      }
    }

    // now compile the list of unprotected active greeting fields
    $fields_to_render = array();
    foreach ($active_fields as $field_id => $field) {
      if (preg_match("#^greeting_field_(?P<field_number>\d+)$#", $field['name'], $matches)) {
        $field_number = $matches['field_number'];
        if (!in_array($field_number, $protected_fields)) {
          // this field is not protected
          $template = CRM_Utils_Array::value("greeting_smarty_{$field_number}", $templates, '');
          $fields_to_render["custom_{$field['id']}"] = $template;
        }
      }
    }

    return $fields_to_render;
  }

  /**
   * Returns a comma-separated list of the fields used in the templates
   */
  public static function getUsedContactFields($templates) {
    $active_fields = CRM_Moregreetings_Config::getActiveFields();
    $fields_used = array();

    // now compile the list of unprotected active greeting fields
    foreach ($active_fields as $field_id => $field) {
      if (preg_match("#^greeting_field_(?P<field_number>\d+)$#", $field['name'], $matches)) {
        $field_number = $matches['field_number'];
        $template = CRM_Utils_Array::value("greeting_smarty_{$field_number}", $templates, '');

        if (preg_match_all('#\$contact\.(?P<field>\w+)#', $template, $tokens)) {
          foreach ($tokens['field'] as $field_name) {
            $fields_used[$field_name] = 1;
          }
        }
      }
    }

    return implode(',', array_keys($fields_used));
  }
}
