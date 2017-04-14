<?php

/**
 * update current greetings
 *
 */

class CRM_Moregreetings_Renderer {

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
      $renderOut = $smarty->fetch("string:$value");
      $greeting_index = $mapping[$key];
      $createArray["custom_{$greeting_index}"] = $renderOut;
    }
    $update_result = civicrm_api3('CustomValue', 'create', $createArray);
  }

  public static function getGreetingMappings() {

    $mapping = array();
    $result = civicrm_api3('CustomField', 'get', array(
      'sequential' => 1,
      'custom_group_id' => "more_greetings_group",
    ));
    foreach ($result['values'] as $value) {
      if (strpos($value['name'], '_protected') !== false) {
        continue;
      } else {
        $mappingIndex = str_replace('greeting_field_', 'greeting_smarty_', $value['name']);
      }
      $mapping[$mappingIndex] = $value['id'];
    }
    return $mapping;
  }
}
