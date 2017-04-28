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


class CRM_Moregreetings_Config {
  private static $customGroup = NULL;
  private static $customFields = NULL;
  /**
   * Get the Moregreetings CustomGroup
   */
  public static function getGroup() {
    if (self::$customGroup === NULL) {
      self::$customGroup = civicrm_api3('CustomGroup', 'getsingle', array('name' => 'more_greetings_group'));
    }
    return self::$customGroup;
  }

  /**
   * Get the Moregreetings CustomGroup ID
   */
  public static function getGroupID() {
    $group = self::getGroup();
    return $group['id'];
  }

  /**
   * load all fields for the custom group
   */
  public static function getFields() {
    if (self::$customFields === NULL) {
      $fields = civicrm_api3('CustomField', 'get', array(
        'custom_group_id' => self::getGroupID(),
        'option.limit'    => 0));
      self::$customFields = $fields['values'];
    }
    return self::$customFields;
  }

  /**
   * get only the currently active fields
   */
  public static function getActiveFields() {
    $fields = self::getFields();
    $active_fields = array();

    foreach ($fields as $field_id => $field) {
      if ($field['is_active']) {
        $active_fields[$field_id] = $field;
      }
    }
    return $active_fields;
  }

  /**
   * loads the current greetings data for a contact
   */
  public static function getCurrentData($contact_id) {
    $field_keys = array();
    $active_fields = self::getActiveFields();
    foreach ($active_fields as $key => $field) {
      $field_keys[] = "custom_{$field['id']}";
    }

    return civicrm_api3('Contact', 'getsingle', array(
      'id'     => $contact_id,
      'return' => implode(',', $field_keys),
    ));
  }
}
