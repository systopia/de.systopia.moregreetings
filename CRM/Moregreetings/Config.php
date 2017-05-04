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
   * Get the number of currently active greeting fields
   */
  public static function getActiveFieldCount() {
    $active_fields = self::getActiveFields();
    $field_counter = 0;
    foreach ($active_fields as $field) {
      if (preg_match("#^greeting_field_(?P<field_number>\\d+)$#", $field['name'])) {
        $field_counter += 1;
      }
    }
    return $field_counter;
  }

  /**
   * Get the maximum number of greeting fields
   */
  public static function getMaxActiveFieldCount() {
    return 9;
  }

  /**
   * Set/adjust the number of active greeting fields
   */
  public static function setActiveFieldCount($count) {
    if ($count < 1 || $count > self::getMaxActiveFieldCount()) {
      throw new Exception("Illegal number of active fields: $count");
    }

    $all_fields = self::getFields();
    $enabled_indexes = range(1, $count);
    foreach ($all_fields as $field) {
      if (preg_match("#^greeting_field_(?P<field_number>\\d+)(_protected)?$#", $field['name'], $matches)) {
        // this is one of our fields...
        if (in_array($matches['field_number'], $enabled_indexes)) {
          // this field should now be active
          if (!$field['is_active']) {
            civicrm_api3('CustomField', 'create', array(
              'id'        => $field['id'],
              'is_active' => 1,
              'data_type' => $field['data_type'],
              'html_type' => $field['html_type'],
              ));
            self::$customFields = NULL; // reset cached data
          }
        } else {
          if ($field['is_active']) {
            // this field should NOT be active any more
            civicrm_api3('CustomField', 'create', array(
              'id'        => $field['id'],
              'is_active' => 0,
              'data_type' => $field['data_type'],
              'html_type' => $field['html_type'],
              ));
            self::$customFields = NULL; // reset cached data
          }
        }
      }
    }
  }

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

  /**
   * Create/enable the cron job to re-calculate all MoreGreetings
   */
  public static function restartCalculateAllGreetingsJob() {
    // find/create cronjob
    $job = self::getAllGreetingsJob();

    // start from zero:
    CRM_Core_BAO_Setting::setItem('0', 'moregreetings', 'moregreetings_job_status');

    // enable cronjob
    if (!$job['is_active']) {
      civicrm_api3('Job', 'create', array(
        'id'        => $job['id'],
        'is_active' => 1));
    }
  }

  /**
   * Disable the cron job to re-calculate all MoreGreetings
   */
  public static function stopCalculateAllGreetingsJob() {
    // find/create cronjob
    $job = self::getAllGreetingsJob();

    if ($job['is_active']) {
      civicrm_api3('Job', 'create', array(
        'id'        => $job['id'],
        'is_active' => 0));
    }
  }


  /**
   * Create/enable the cron job to re-calculate all MoreGreetings
   */
  public static function getAllGreetingsJob() {
    // find/create cronjob
    $jobs = civicrm_api3('Job', 'get', array(
      'api_entity' => 'job',
      'api_action' => 'update_moregreetings'));
    if ($jobs['count'] == 0) {
      $job = array(
        'name' => ts("Update MoreGreetings", array('domain' => 'de.systopia.moregreetings')),
        'description'   => ts("Will update all the 'MoreGreetings' fields, e.g. after a change to the templates. This job will enable/disable itself.", array('domain' => 'de.systopia.moregreetings')),
        'run_frequency' => 'Always',
        'is_active'     => 0,
        'api_entity'    => 'job',
        'api_action'    => 'update_moregreetings');
      $result = civicrm_api3('Job', 'create', $job);
      $job['id'] = $result['id'];
      return $job;
    } else {
      return reset($jobs['values']);
    }
  }
}
