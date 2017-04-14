<?php

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Moregreetings_Form_Settings extends CRM_Core_Form {

  private static $number_of_greetings = 2;

  public function buildQuickForm() {

    $this->registerRule('is_valid_smarty', 'callback', 'validateSmarty', 'CRM_Moregreetings_Form_Settings');

    $this->assign('greetings_count', range(1,self::getNumberOfGreetings()));
    for ($i = 1; $i <= self::getNumberOfGreetings(); ++$i) {
      $this->add(
        'textarea', // field type
        "greeting_smarty_{$i}", // field name
        "Greetings {$i}", // field label
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
        'name' => ts('Submit'),
        'isDefault' => TRUE,
      ),
    ));

    // export form elements
    // $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  public function setDefaultValues() {

    return CRM_Core_BAO_Setting::getItem('moregreetings', 'moregreetings_templates');
  }

  public function postProcess() {
    $values = $this->exportValues();
    for ($i = 1; $i <= self::getNumberOfGreetings(); ++$i) {
      if (isset($values["greeting_smarty_{$i}"])) {

        $values_array["greeting_smarty_{$i}"] =  $values["greeting_smarty_{$i}"];
      } else {
        $values_array["greeting_smarty_{$i}"] = "";
      }
    }
    CRM_Core_BAO_Setting::setItem($values_array, 'moregreetings', 'moregreetings_templates');
    parent::postProcess();
  }


  public static function validateSmarty($smartyValue) {
    if ($smartyValue === "") {
      return TRUE;
    }
    $smarty = CRM_Core_Smarty::singleton();
    $renderOut = $smarty->fetch("string:$smartyValue");
    error_log("pbaDebug render out: $renderOut");
    if (empty($renderOut)) {
      return FALSE;
    }
    return TRUE;
  }

  public static function getNumberOfGreetings() {

    return self::$number_of_greetings;
  }
}
