<?php
/*-------------------------------------------------------+
| SYSTOPIA - MORE GREETINGS EXTENSION                    |
| Copyright (C) 2019 SYSTOPIA                            |
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

use Civi\API\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use \Civi\Core\Event\GenericHookEvent;

/**
 * Implements a resolver to resolve conflicts in the MoreGreetings fields
 */
class CRM_Xdedupe_Resolver_MoreGreetings extends CRM_Xdedupe_Resolver {

    /**
   * get the name of the finder
   * @return string name
   */
  public function getName() {
    return ts("More Greetings", array('domain' => 'de.systopia.moregreetings'));
  }

  /**
   * get an explanation what the finder does
   * @return string name
   */
  public function getHelp() {
    return ts("Will make sure differing MoreGreetings values will not get in the way, by dropping them before the merge and recalculating them after.", array('domain' => 'de.systopia.moregreetings'));
  }

  /**
   * Resolve the problem by simply deleting the records for all contacts
   *
   * @param $main_contact_id    int     the main contact ID
   * @param $other_contact_ids  array   other contact IDs
   * @return boolean TRUE, if there was a conflict to be resolved
   * @throws Exception if the conflict couldn't be resolved
   */
  public function resolve($main_contact_id, $other_contact_ids) {
    $all_contact_ids = array_merge($other_contact_ids, [$main_contact_id]);
    if (!empty($all_contact_ids)) {
      $all_contact_ids_list = implode(',', $all_contact_ids);
      CRM_Core_DAO::executeQuery("DELETE FROM civicrm_value_moregreetings WHERE entity_id IN ({$all_contact_ids_list})");
    }

    return TRUE;
  }

  /**
   * simply re-calculate the main contact
   *
   * @param $main_contact_id    int     the main contact ID
   * @param $other_contact_ids  array   other contact IDs
   * @throws Exception if the conflict couldn't be resolved
   */
  public function postProcess($main_contact_id, $other_contact_ids) {
    CRM_Moregreetings_Renderer::updateMoreGreetings($main_contact_id);
    // todo: other contacts, too? no, right!?
  }

}
