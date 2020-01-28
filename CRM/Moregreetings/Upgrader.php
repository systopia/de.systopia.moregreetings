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
class CRM_Moregreetings_Upgrader extends CRM_Moregreetings_Upgrader_Base {

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

}
