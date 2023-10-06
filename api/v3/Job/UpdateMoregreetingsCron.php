<?php

function _civicrm_api3_job_update_moregreetings_cron_spec(&$spec) {
}

function civicrm_api3_job_update_moregreetings_cron($params) {
  $cron = CRM_Moregreetings_Job::launchCron();
  return civicrm_api3_create_success($cron, $params);
}
