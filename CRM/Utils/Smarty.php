<?php
/*-------------------------------------------------------+
| SYSTOPIA CUSTOM DATA HELPER -- VERSION 0.1             |
| Copyright (C) 2017 SYSTOPIA                            |
| Author: B. Endres (endres@systopia.de)                 |
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
 * provides some useful smarty functions
 */
class CRM_Utils_Smarty {

  /**
   * register custom smarty functions with the smarty instance
   *
   * @param Smarty $smarty
   */
  public static function registerCustomFunctions($smarty) {
    $smarty->register_modifier('mg_startswith', array('CRM_Utils_Smarty', 'startswith'));
    $smarty->register_modifier('mg_endswith',   array('CRM_Utils_Smarty', 'endswith'));
    $smarty->register_modifier('mg_contains',   array('CRM_Utils_Smarty', 'contains'));
    $smarty->register_modifier('lcfirst',    'lcfirst');
    $smarty->register_modifier('ucfirst',    'ucfirst');
  }

  /**
   * Checks whether the modified string starts with $prefix
   * Smarty usage: {if $contact.formal_title|mg_startswith:'Dr'}
   */
  public static function startswith($string, $prefix) {
    return substr($string, 0, strlen($prefix)) == $prefix;
  }

  /**
   * Checks whether the modified string ends with $suffix
   * Smarty usage: {if $contact.formal_title|mg_endswith:'Dr'}
   */
  public static function endswith($string, $suffix) {
    return substr($string, (strlen($string) - strlen($suffix))) == $suffix;
  }

  /**
   * Checks whether the modified string contains $substring
   * Smarty usage: {if $contact.formal_title|mg_contains:'Dr'}
   */
  public static function contains($string, $substring) {
    if (strlen($string) == 0) {
      return strlen($substring) == 0;
    } else {
      if (strlen($substring) == 0) {
        return FALSE;
      } else {
        $strstr = strstr($string, $substring);
        return strlen($strstr) > 0;
      }
    }
  }
}
