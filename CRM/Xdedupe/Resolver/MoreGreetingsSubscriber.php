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
 * Subscribe to XDedupe events, so we can register our resolver
 */
class CRM_Xdedupe_Resolver_MoreGreetingsSubscriber implements EventSubscriberInterface {

  /**
   * Subscribe to the list events, so we can plug the built-in ones
   */
  public static function getSubscribedEvents(): array {
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
}
