<?php
/*-------------------------------------------------------+
| SYSTOPIA - MORE GREETINGS EXTENSION                    |
| Copyright (C) 2017-2020 SYSTOPIA                       |
| Author: B. Endres (endres@systopia.de)                 |
|         P. Batroff (batroff@systopia.de)               |
|         J. Schuppe (schuppe@systopia.de)               |
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

use CRM_Moregreetings_ExtensionUtil as E;

/**
 * Collection of upgrade steps.
 */
class CRM_Moregreetings_Upgrader extends CRM_Extension_Upgrader_Base {

  /**
   * Update greetings fields to make them searchable and displayable in reports.
   *
   * @return bool TRUE on success
   * @throws Exception
   */
  public function upgrade_5001() {
    // Update custom fields specification, this should alter the "is_searchable"
    // property.
    $customData = new CRM_Moregreetings_CustomData(E::LONG_NAME);
    $customData->syncCustomGroup(
      E::path('/resources/moregreetings_custom_group.json'),
      array(
        'is_searchable',
      )
    );

    return TRUE;
  }

  /**
   * Make sure users get a warning if any of their
   *
   * @return bool TRUE on success
   * @throws Exception
   */
  public function upgrade_5002() {
    CRM_Moregreetings_Upgrader::conveyTokenWarnings();
    return TRUE;
  }

  /**
   * This function will give the user a warning if tokens are used that have been
   *   discontinued, e.g. through the switch to apiv4
   *
   * @see https://github.com/systopia/de.systopia.moregreetings/issues/48
   *
   * @return void
   */
  public static function conveyTokenWarnings()
  {
    // 1. if there are 'individual_prefix' tokens in our templates tell the users to switch to 'prefix_id:label'
    self::notifyTokenUsed('individual_prefix', E::ts('Use {%1} instead.', [1=>'$prefix_id:label']));
  }

  /**
   * Check if the given token is in use.
   *
   * @param string $token
   *   the token that has been discontinued
   *
   * @param string $advice
   *   advice to offer as replacement
   */
  public static function notifyTokenUsed($token, $advice)
  {
    $group = CRM_Moregreetings_Config::getGroup();
    if (empty($group['table_name'])) {
      Civi::log()->warning("Table name for more greetings could not be determined.");
      return;
    }
    $fields = CRM_Moregreetings_Config::getActiveFields();
    foreach ($fields as $field) {
      if ($field['data_type'] == 'String') {
        $token_expression = "LIKE \"\{\${$token}\}\"";
        $query = "SELECT COUNT(*) FROM {$group['table_name']} WHERE {$field['column_name']} {$token_expression}";
        $is_used = CRM_Core_DAO::singleValueQuery($query);
        if ($is_used) {
          CRM_Core_Session::setStatus(
            E::ts("Discontinued token '\{%1\}' is still used in your templates. " . $advice, [1=>$token]),
            E::ts("Warning"), 'alert', ['expires' => 0]);
          }
        break;
      }
    }
  }
}
