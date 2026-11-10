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

declare(strict_types = 1);

/**
 * provides some useful smarty functions
 */
final class CRM_Moregreetings_Utils_Smarty {

  /**
   * @param array<string, mixed> $vars
   *
   * @throws \CRM_Core_Exception
   */
  public static function parseOneOffStringThroughSmarty(string $string, array $vars = []): string {
    self::registerCustomModifiers();

    return \CRM_Utils_String::parseOneOffStringThroughSmarty($string, $vars);
  }

  /**
   * Split the given string by the given $separator into individual tokens,
   *   and then check if all these tokens have a minimum length of $minTokenLength
   * If $tokenIndices is present, apply only to the tokens with the given indices
   *
   * @param string $string
   *    the input string
   * @param non-empty-string $separator
   *    the term used to split the string
   * @param int $minTokenLength
   *    the minimum length to be tested for
   * @param list<int|string>|string $tokenIndices
   *    only check the given indices instead of all - provided they exist.
   * @param bool $all
   *    test if all the tokens have minimum length, or just some
   * @param bool $trim
   *    trims the tokens with the default trim set
   *
   * Smarty usage: {if $contact.first_name|tokens_have_min_length:' ':2}
   *     is true, if no single characters are used in the first_name
   */
  // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh
  public static function tokens_have_min_length(
    string $string,
    string $separator,
    int $minTokenLength,
    array|string|null $tokenIndices = NULL,
    bool $all = TRUE,
    bool $trim = TRUE
  ): bool {
    $tokens = explode($separator, $string);

    if ($trim) {
      $tokens = array_map('trim', $tokens);
    }

    // default for token indices is all
    if ($tokenIndices === NULL) {
      $tokenIndices = array_keys($tokens);
    }
    // if somebody just passed a single one or a comma separated string, turn that into an array
    elseif (is_string($tokenIndices)) {
      $tokenIndices = explode(',', $tokenIndices);
    }

    if ($all) {
      // now check if all of them have the minimal length
      foreach ($tokens as $index => $token) {
        // @phpstan-ignore function.strict
        if (in_array($index, $tokenIndices, FALSE)) {
          if (strlen($token) < $minTokenLength) {
            return FALSE;
          }
        }
      }
      return TRUE;
    }
    else {
      // now check if some of them have the minimal length
      foreach ($tokens as $index => $token) {
        // @phpstan-ignore function.strict
        if (in_array($index, $tokenIndices, FALSE)) {
          if (strlen($token) >= $minTokenLength) {
            return TRUE;
          }
        }
      }
      return FALSE;
    }
  }

  /**
   * Split the given string by the given $separator into individual tokens,
   *   and extract the token with the given index
   *
   * @param string $string
   *    the input string
   * @param non-empty-string $separator
   *    the term used to split the string
   * @param int $index
   *    the index of the token to return
   * @param bool $trim
   *    trims the token with the default trim set
   *
   * Smarty usage: {$contact.first_name|token_extract:' ':1}
   *    will return the 'van' if the first_name is "Herbert van Halen"
   */
  public static function token_extract(
    string $string,
    string $separator,
    int $index = 0,
    bool $trim = TRUE
  ): string {
    $tokens = explode($separator, $string);
    $token = $tokens[$index] ?? '';
    if ($trim) {
      return trim($token);
    }
    else {
      return $token;
    }
  }

  private static function registerCustomModifiers(): void {
    static $registered = FALSE;
    if (!$registered) {
      $smarty = \CRM_Core_Smarty::singleton();
      if (NULL === $smarty->getRegisteredPlugin('modifier', 'mg_startswith')) {
        $smarty->registerPlugin('modifier', 'mg_startswith', 'str_starts_with');
      }

      if (NULL === $smarty->getRegisteredPlugin('modifier', 'mg_endswith')) {
        $smarty->registerPlugin('modifier', 'mg_endswith', 'str_ends_with');
      }

      if (NULL === $smarty->getRegisteredPlugin('modifier', 'mg_contains')) {
        $smarty->registerPlugin('modifier', 'mg_contains', 'str_contains');
      }

      if (NULL === $smarty->getRegisteredPlugin('modifier', 'tokens_have_min_length')) {
        $smarty->registerPlugin('modifier', 'tokens_have_min_length', [__CLASS__, 'tokens_have_min_length']);
      }

      if (NULL === $smarty->getRegisteredPlugin('modifier', 'token_extract')) {
        $smarty->registerPlugin('modifier', 'token_extract', [__CLASS__, 'token_extract']);
      }

      if (NULL === $smarty->getRegisteredPlugin('modifier', 'lcfirst')) {
        $smarty->registerPlugin('modifier', 'lcfirst', 'lcfirst');
      }

      if (NULL === $smarty->getRegisteredPlugin('modifier', 'ucfirst')) {
        $smarty->registerPlugin('modifier', 'ucfirst', 'ucfirst');
      }

      $registered = TRUE;
    }
  }

}
