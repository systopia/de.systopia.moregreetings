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
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Moregreetings_Form_Settings extends CRM_Core_Form {

  /**
   * @var string | array | NULL
   *   Used to store the original error handler, which will be temporarily
   *   replaced for identifying smarty errors when saving the MoreGreetings
   *   configuration form.
   */
  protected static $_original_error_handler = NULL;

  public function buildQuickForm() {

    $this->registerRule('is_valid_smarty', 'callback', 'validateSmarty', 'CRM_Moregreetings_Form_Settings');
    $this->registerRule('is_valid_field_count', 'callback', 'validateFieldCount', 'CRM_Moregreetings_Form_Settings');

    $this->add(
      'text',
      "greeting_count",
      ts("Number of fields", array('domain' => 'de.systopia.moregreetings'))
    );

    $this->addRule("greeting_count",
                  ts('Please enter a number between 1 and %1', array(
                    1 => CRM_Moregreetings_Config::getMaxActiveFieldCount(),
                    'domain' => 'de.systopia.moregreetings')),
                  'is_valid_field_count');

    $this->assign('greetings_count', range(1,self::getNumberOfGreetings()));
    for ($i = 1; $i <= self::getNumberOfGreetings(); ++$i) {
      $this->add(
        'textarea', // field type
        "greeting_smarty_{$i}", // field name
        ts("Greeting %1", array(1 => $i, 'domain' => 'de.systopia.moregreetings')), // field label
        array('rows' => 4,
              'cols' => 50,
        ), // list of options
        FALSE // is required
      );
      $this->addRule("greeting_smarty_{$i}",
                    ts('Please enter valid smarty code.', array('domain' => 'de.systopia.moregreetings')),
                    'is_valid_smarty'
      );
    }
    // add form elements

    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => ts('Save', array('domain' => 'de.systopia.moregreetings')),
        'isDefault' => TRUE,
      ),
      array(
        'type' => 'upload',
        'name' => ts('Save & Apply to all Contacts', array('domain' => 'de.systopia.moregreetings')),
        'isDefault' => FALSE,
      ),
    ));

    // add link
    $group_id  = CRM_Moregreetings_Config::getGroupID();
    $group_url = CRM_Utils_System::url('civicrm/admin/custom/group/field', "reset=1&action=browse&gid={$group_id}");
    $this->assign('group_url', $group_url);

    // export form elements
    parent::buildQuickForm();
  }

  /**
   * set the default (=current) values in the form
   */
  public function setDefaultValues() {
    $values = CRM_Core_BAO_Setting::getItem('moregreetings', 'moregreetings_templates');
    if (!is_array($values)) {
      $values = array();
    }
    $values['greeting_count'] = self::getNumberOfGreetings();
    return $values;
  }


  /**
   * POST PROCESS: Store the new values
   */
  public function postProcess() {
    $values = $this->exportValues();

    // first: update the greetings
    $old_greetings = CRM_Core_BAO_Setting::getItem('moregreetings', 'moregreetings_templates');
    $greetings_changed = FALSE;
    for ($i = 1; $i <= self::getNumberOfGreetings(); ++$i) {
      if (isset($values["greeting_smarty_{$i}"])) {
        $values_array["greeting_smarty_{$i}"] =  $values["greeting_smarty_{$i}"];
      } else {
        $values_array["greeting_smarty_{$i}"] = "";
      }

      // check if it changed
      if (CRM_Utils_Array::value("greeting_smarty_{$i}", $old_greetings) != $values_array["greeting_smarty_{$i}"]) {
        $greetings_changed = TRUE;
      }
    }
    CRM_Core_BAO_Setting::setItem($values_array, 'moregreetings', 'moregreetings_templates');

    // then: adjust the greeting count
    if ($values['greeting_count'] != self::getNumberOfGreetings()) {
      CRM_Moregreetings_Config::setActiveFieldCount($values['greeting_count']);

      // reload b/c the form has already been generated
      $url = CRM_Utils_System::url('civicrm/admin/setting/moregreetings', "reset=1");
      CRM_Utils_System::redirect($url);
    }

    if (isset($values['_qf_Settings_upload'])) {
      // somebody pressed the SAVE & APPLY button:
      CRM_Moregreetings_Job::launchApplicationRunner(); // doesn't return
    }

    parent::postProcess();
  }


  /**
   * Form validation rule
   */
  public static function validateSmarty($smartyValue) {
    if ($smartyValue === "") {
      return TRUE;
    }

    try {
      $smarty = CRM_Core_Smarty::singleton();
      CRM_Utils_Smarty::registerCustomFunctions($smarty);

      // Smarty uses trigger_error() to indicate Smarty errors. In order to
      // fetch those, replace the current error handler with a custom one, which
      // will throw an exception, that will be caught here. Store as a static
      // class member in order to access it within the custom error handler.
      static::$_original_error_handler = set_error_handler(array(get_class(), 'smartyErrorHandler'));

      // Try the rendering.
      try {
        $renderOut = $smarty->fetch('string:' . $smartyValue);
      } catch (ErrorException $exception) {
        // Coming from the custom error handler.
        $renderOut = FALSE;
      }

      if (!is_string($renderOut)) {
        return FALSE;
      }
    } catch (Exception $e) {
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Error handler that throws an exception, that can be caught and Smarty
   * errors be identified.
   *
   * @param $errNo
   * @param $errStr
   * @param $errFile
   * @param $errLine
   *
   * @throws \ErrorException
   */
  public static function smartyErrorHandler($errNo, $errStr, $errFile, $errLine, $errContext = []) {
    // Call the original error handler with the original error parameters. This
    // makes sure the error still gets printed or logged or whatever the
    // original error handler is supposed to do with it.
    call_user_func(
      static::$_original_error_handler,
      $errNo,
      $errStr,
      $errFile,
      $errLine,
      $errContext
    );

    // Restore the original error handler for subsequent error handling.
    restore_error_handler();

    if (strpos($errStr, 'Smarty error:') === 0) {
      throw new ErrorException(
        $errStr,
        $errNo,
        1,
        $errFile,
        $errLine
      );
    }
  }

  /**
   * Form validation rule
   */
  public static function validateFieldCount($field_count) {
    return ($field_count > 0 && $field_count <= CRM_Moregreetings_Config::getMaxActiveFieldCount());
  }


  public static function getNumberOfGreetings() {
    return CRM_Moregreetings_Config::getActiveFieldCount();
  }
}
