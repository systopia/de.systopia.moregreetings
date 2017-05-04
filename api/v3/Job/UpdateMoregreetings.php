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

/**
 * Cron Job to update the more-greetings for all contacts
 */
function civicrm_api3_job_update_moregreetings($params) {
  $last_id = CRM_Core_BAO_Setting::getItem('moregreetings', 'moregreetings_job_status');
  if ($last_id == 'busy') {
    // there's another job running
    return civicrm_api3_create_success(ts("Job already running", array('domain' => 'de.systopia.moregreetings')));
  }

  // ok, let's go
  CRM_Core_BAO_Setting::setItem('busy', 'moregreetings', 'moregreetings_job_status');
  $start_time = microtime(TRUE);

  // run the renderer on blocks of contacts until the time runs out
  while ( (microtime(TRUE)-$start_time) < $params['max_time'] ) {
    $next_id = (int) $last_id + 1;
    $last_id = CRM_Moregreetings_Renderer::updateMoreGreetingsForContacts($next_id, $params['block_size']);

    if ($last_id == 0) {
      // done
      break;
    }
  }

  if ($last_id == 0) {
    // we're done!
    CRM_Moregreetings_Config::stopCalculateAllGreetingsJob();
    return civicrm_api3_create_success(ts("Done.", array('domain' => 'de.systopia.moregreetings')));
  } else {
    // store last processed ID
    CRM_Core_BAO_Setting::setItem($last_id, 'moregreetings', 'moregreetings_job_status');
    return civicrm_api3_create_success(ts("Interrupted processing, more contacts remain.", array('domain' => 'de.systopia.moregreetings')));
  }
}


function _civicrm_api3_job_update_moregreetings_spec(&$params) {
  $params['max_time'] = array(
    'name'        => 'max_time',
    'uniqueName'  => 'max_time',
    'title'       => ts('Maximum Runtime', array('domain' => 'de.systopia.moregreetings')),
    'description' => ts('Maximum runtime in seconds', array('domain' => 'de.systopia.moregreetings')),
    'api.default' => 120,
  );
  $params['block_size'] = array(
    'name'        => 'block_size',
    'uniqueName'  => 'block_size',
    'title'       => ts('Block Size', array('domain' => 'de.systopia.moregreetings')),
    'description' => ts('How many contacts to process in one iteration', array('domain' => 'de.systopia.moregreetings')),
    'api.default' => 50,
  );
}
