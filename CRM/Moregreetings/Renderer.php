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

  public static function updateMoreGreetings($contact_id) {

    $templates = CRM_Core_BAO_Setting::getItem('moregreetings', 'moregreetings_templates');
    if (!is_array($templates)) {
      return NULL;
    }

    $contact = civicrm_api3('Contact', 'getsingle', array(
      'id' => $contact_id,
    ));

    $smarty = CRM_Core_Smarty::singleton();
    $mapping = CRM_Moregreetings_Renderer::getGreetingMappings();
    $createArray = array(
      'entity_id' => $contact_id,
      'entity_table' => "civicrm_contact"
    );

    foreach ($templates as $key => $value) {

      if(CRM_Moregreetings_Renderer::checkProtectedField($contact_id, $mapping['protected'][$key])) {
        continue;
      }
      $renderOut = $smarty->fetch("string:$value");
      $greeting_index = $mapping['greetings'][$key];
      $createArray["custom_{$greeting_index}"] = $renderOut;
    }
    $update_result = civicrm_api3('CustomValue', 'create', $createArray);
  }

  public static function checkProtectedField($contactId, $customFieldId) {

    $result = civicrm_api3('Contact', 'getsingle', array(
      'sequential' => 1,
      'return' => array("custom_{$customFieldId}"),
      'id' => $contactId,
    ));
    if (empty($result["custom_{$customFieldId}"]) || $result["custom_{$customFieldId}"] === 0)  {
      return FALSE;
    } else if ($result["custom_{$customFieldId}"] == 1) {
      return TRUE;
    } else {
      error_log("Undefined value for Conatct {$contactId} in field custom_{$customFieldId}");
      return NULL;
    }
  }

  public static function getGreetingMappings() {

    $mapping = array();
    $result = civicrm_api3('CustomField', 'get', array(
      'sequential' => 1,
      'custom_group_id' => "more_greetings_group",
    ));
    foreach ($result['values'] as $value) {
      if (strpos($value['name'], '_protected') !== false) {
        $mappingIndex = str_replace('_protected', '', $value['name']);
        $mappingIndex = str_replace('greeting_field_', 'greeting_smarty_', $mappingIndex);
        $mapping['protected'][$mappingIndex] = $value['id'];
        continue;
      } else {
        $mappingIndex = str_replace('greeting_field_', 'greeting_smarty_', $value['name']);
        $mapping['greetings'][$mappingIndex] = $value['id'];
      }
    }
    return $mapping;
  }
}
