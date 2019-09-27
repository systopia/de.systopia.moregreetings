<?php
/*-------------------------------------------------------+
| SYSTOPIA's Extended Deduper                            |
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

use CRM_Xdedupe_ExtensionUtil as E;

use Civi\API\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use \Civi\Core\Event\GenericHookEvent;

/**
 * Implements a resolver for basic contact fields
 */
class CRM_Xdedupe_Resolver_MoreGreetings extends CRM_Xdedupe_Resolver implements EventSubscriberInterface {

  /**
   * Subscribe to the list events, so we can plug the built-in ones
   */
  public static function getSubscribedEvents() {
    return [
        'civi.xdedupe.resolvers' => ['addBuiltinResolvers', Events::W_MIDDLE],
    ];
  }

  /**
   * Return the list of built-in resolvers
   */
  public function addBuiltinResolvers(GenericHookEvent $xdedupe_list) {
    $xdedupe_list->list[] = 'CRM_Xdedupe_Resolver_MoreGreetings';
  }

    /**
   * get the name of the finder
   * @return string name
   */
  public function getName() {
    return E::ts("More Greetings");
  }

  /**
   * get an explanation what the finder does
   * @return string name
   */
  public function getHelp() {
    return E::ts("Will make sure differing MoreGreetings values will not get in the way, by dropping them before the merge and recalculating them after.");
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
