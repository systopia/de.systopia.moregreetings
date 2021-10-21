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
    $smarty->register_modifier('mg_startswith',            ['CRM_Utils_Smarty', 'startswith']);
    $smarty->register_modifier('mg_endswith',              ['CRM_Utils_Smarty', 'endswith']);
    $smarty->register_modifier('mg_contains',              ['CRM_Utils_Smarty', 'contains']);
    $smarty->register_modifier('tokens_have_min_length',   ['CRM_Utils_Smarty', 'tokens_have_min_length']);
    $smarty->register_modifier('token_extract',            ['CRM_Utils_Smarty', 'token_extract']);
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

  /**
   * Split the given string by the given $split_string into individual tokens,
   *   and then check if all these tokens have a minimum length of $length
   * If $token_indices is present, apply only to the tokens with the given indices
   *
   * @param $string string
   *    the input string
   * @param $split_string string
   *    the term used to split the string
   * @param $length integer
   *    the minimum length to be tested for
   * @param $token_indices array|string
   *    only check the given indices instead of all - provided they exist.
   * @param $all boolean
   *    test if all the tokens have minimum length, or just some
   * @param $trim boolean
   *    trims the tokens with the default trim set
   *
   * Smarty usage: {if $contact.first_name|tokens_have_min_length:' ':2}
   *     is true, if no single characters are used in the first_name
   */
  public static function tokens_have_min_length($string, $split_string, $length, $token_indices = null, $all = true, $trim = true) {
    $length = (int) $length;
    $tokens = explode($split_string, $string);

    if ($trim) {
      $tokens = array_map('trim', $tokens);
    }

    // default for token indices is all
    if ($token_indices === null) {
      $token_indices = array_keys($tokens);
    }

    // if somebody just passed a single one or a comma separated string, turn that into an array
    if (!is_array($token_indices)) {
      $token_indices = explode(',', $token_indices);
    }

    if ($all) {
      // now check if all of them have the minimal length
      foreach ($tokens as $index => $token) {
        if (isset($token_indices[$index])) {
          if (strlen($token) < $length) {
            return false;
          }
        }
      }
      return true;
    } else {
      // now check if some of them have the minimal length
      foreach ($tokens as $index => $token) {
        if (isset($token_indices[$index])) {
          if (strlen($token) >= $length) {
            return true;
          }
        }
      }
      return false;

    }
  }

  /**
   * Split the given string by the given $split_string into individual tokens,
   *   and extract the token with the given index
   *
   * @param $string string
   *    the input string
   * @param $split_string string
   *    the term used to split the string
   * @param $index integer
   *    the index of the token to return
   * @param $trim boolean
   *    trims the token with the default trim set
   *
   * Smarty usage: {$contact.first_name|token_extract:' ':1}
   *    will return the 'van' if the first_name is "Herbert van Halen"
   */
  public static function token_extract($string, $split_string, $index = 0, $trim = true) {
    $index = (int) $index;
    $tokens = explode($split_string, $string);
    $token = $tokens[$index] ?? '';
    if ($trim) {
      return trim($token);
    } else {
      return $token;
    }
  }
}
