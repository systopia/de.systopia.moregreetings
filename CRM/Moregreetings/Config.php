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
}
