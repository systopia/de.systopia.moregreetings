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

  // update fieldName => update fieldValue
  // TODO:
  public static function updateAllGreetings($customFieldName) {

    if (!is_array($templates)) {
      return NULL;
    }
    $result = civicrm_api3('Contact', 'get', array(
      'sequential' => 1,
      'return' => array("id"),
      'contact_type' => "Individual",
      'is_deleted' => 0,
      'options' => array('limit' => 0),
    ));
    $smarty = CRM_Core_Smarty::singleton();
    $createArray = array(
      'entity_table' => "civicrm_contact"
    );

    // get the template value for the specified field name
    $templates = CRM_Core_BAO_Setting::getItem('moregreetings', 'moregreetings_templates');
    $renderOut = $smarty->fetch("string:$templates[$customFieldName]");
    // get the field id for the specified field name
    $mapping = CRM_Moregreetings_Renderer::getGreetingMappings();
    $greeting_index = $mapping[$customFieldName];
    // add the custom field name to the edit command
    $createArray["custom_{$greeting_index}"] = $renderOut;

    //iterate over all contacts
    foreach ($result as $value) {
      $createArray['entity_id'] = $value['contact_id'];
      civicrm_api3('CustomValue', 'create', $createArray);
    }
  }

  /**
   * Re-calculate the more-greetings for one contact
   */
  public static function updateMoreGreetings($contact_id) {
    // load the templates
    $templates = CRM_Core_BAO_Setting::getItem('moregreetings', 'moregreetings_templates');
    if (!is_array($templates)) {
      return NULL;
    }

    // load the contact
    $contact = civicrm_api3('Contact', 'getsingle', array(
      'id' => $contact_id,
    ));

    // TODO: assign more stuff?
    // prepare smarty
    $smarty = CRM_Core_Smarty::singleton();
    $smarty->assign('contact', $contact);

    // load the current greetings
    $current_greetings = CRM_Moregreetings_Config::getCurrentData($contact_id);

    // get the fields to render
    $greetings_to_render = self::getGreetingsToRender($contact, $templates, $current_greetings);

    // render the greetings
    $greetings_update = array();
    foreach ($greetings_to_render as $greeting_key => $template) {
      $new_value = $smarty->fetch("string:$template");
      // check if the value is really different (avoid unecessary updates)
      if ($new_value != $current_greetings[$greeting_key]) {
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
}
