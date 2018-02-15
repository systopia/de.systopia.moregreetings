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

define('MOREGREETINGS_JOB_SIZE', 250);
/**
 * Runner Job to apply the current templates to all contacts
 */
class CRM_Moregreetings_Job {

  public $title     = NULL;
  protected $offset = NULL;
  protected $count  = NULL;


  protected function __construct($offset, $count) {
    $this->offset  = $offset;
    $this->count   = $count;

    // set title
    $this->title = ts("Updating moregreetings on contacts %1-%2", array(
          1 => $this->offset, 2 => $this->offset + $this->count, 'domain' => 'de.systopia.moregreetings'));
  }

  public function run($context) {
    // get contact IDs
    $id_query = CRM_Core_DAO::executeQuery("
      SELECT id AS contact_id
      FROM civicrm_contact
      WHERE is_deleted = 0
      LIMIT {$this->count}
      OFFSET {$this->offset}");
    $contact_ids = array();
    while ($id_query->fetch()) {
      $contact_ids[] = $id_query->contact_id;
    }

    // determine the fields to load
    $templates = CRM_Core_BAO_Setting::getItem('moregreetings', 'moregreetings_templates');
    $used_fields = CRM_Moregreetings_Renderer::getUsedContactFields($templates);

    $active_fields = CRM_Moregreetings_Config::getActiveFields();
    foreach ($active_fields as $key => $field) {
      $field_keys[] = "custom_{$field['id']}";
    }

    // load contacts
    // remark: if you change these parameters, see if you also want to adjust
    //  CRM_Moregreetings_Renderer::updateMoreGreetings and CRM_Moregreetings_Renderer::updateMoreGreetingsForContacts
    $contacts = civicrm_api3('Contact', 'get', array(
      'id'           => array('IN' => $contact_ids),
      'return'       => $used_fields . ',' . implode($field_keys, ','),
      'option.limit' => 0));

    // apply
    foreach ($contacts['values'] as $contact) {
      CRM_Moregreetings_Renderer::updateMoreGreetings($contact['id'], $contact);
    }

    return TRUE;
  }

  /**
   * Use CRM_Queue_Runner to apply the templates
   * This doesn't return, but redirects to the runner
   */
  public static function launchApplicationRunner() {
    $queue = self::prepareQueue('moregreetings_application');
    // create a runner and launch it
    $runner = new CRM_Queue_Runner(array(
      'title'     => ts("Applying Moregreetings Templates", array('domain' => 'de.systopia.moregreetings')),
      'queue'     => $queue,
      'errorMode' => CRM_Queue_Runner::ERROR_ABORT,
      'onEndUrl'  => CRM_Utils_System::url('civicrm/admin/setting/moregreetings', "reset=1"),
    ));
    $runner->runAllViaWeb(); // does not return
  }

  /**
   * Use CRM_Queue_Runner to apply the templates
   * This doesn't redirect to the runner
   */
  public static function launchCron() {
    $queue = self::prepareQueue('moregreetings_cron');
    $runner = new CRM_Queue_Runner(array(
      'title'     => ts("Applying Moregreetings Templates by Cron Job", array('domain' => 'de.systopia.moregreetings')),
      'queue'     => $queue,
      'errorMode' => CRM_Queue_Runner::ERROR_ABORT,
    ));
    return $runner->runAll();
  }

  /**
   * Prepare queue.
   *
   * @param string $name Name of queue
   *
   * @return \CRM_Queue_Queue
   */
  private static function prepareQueue($name) {
    // get general contact count (not deleted)
    $contact_count = CRM_Core_DAO::singleValueQuery("SELECT COUNT(id) FROM civicrm_contact WHERE is_deleted=0");

    // create a queue
    $queue = CRM_Queue_Service::singleton()->create(array(
      'type'  => 'Sql',
      'name'  => $name,
      'reset' => TRUE,
    ));

    // create the items
    for ($offset = 0; $offset < $contact_count; $offset += MOREGREETINGS_JOB_SIZE) {
      $queue->createItem(new CRM_Moregreetings_Job($offset, MOREGREETINGS_JOB_SIZE));
    }

    return $queue;
  }

}
