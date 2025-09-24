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
   * Make sure users get a warning about the switch to APIv4
   *
   * @return bool TRUE on success
   * @throws Exception
   */
  public function upgrade_5002() {
    CRM_Core_Session::setStatus(
      E::ts('MoreGreetings has switched to APIv4, which means that some tokens might not work any more.') . '<br/><br/>' .
           E::ts('An example would be the defunct <code>$individual_prefix</code> token, which can be substituted by the <code>prefix_id:label</code>') . '<br/><br/>'.
           E::ts('Make sure you test your greeting templates well before you continue using MoreGreetings.'),
      E::ts("Warning"),
      'alert', ['expires' => 0]);
    return TRUE;
  }

  /**
   * Make sure the legacy token option is set with the upgrader if undefined,
   *   because that means that the user is coming from an older version
   *
   * @return bool TRUE on success
   */
  public function upgrade_5003() {
    $current_value = Civi::settings()->get('more_greetings_render_legacy_tokens');
    if ($current_value === NULL) {
      Civi::settings()->set('more_greetings_render_legacy_tokens', 1);
      Civi::log()->info('MoreGreetings: enabled generation of legacy tokens like "custom_12".');
    }
    return TRUE;
  }
}
