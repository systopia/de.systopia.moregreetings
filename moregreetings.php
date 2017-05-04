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

require_once 'moregreetings.civix.php';

/**
 * implement the hook to customize the rendered tab of our custom group
 */
function moregreetings_civicrm_pageRun( &$page ) {
  if ($page->getVar('_name') == 'CRM_Contact_Page_View_Summary') {
      $script = file_get_contents(__DIR__ . '/js/render_moregreetings_view.js');
      $script = str_replace('MOREGREETINGS', CRM_Moregreetings_Config::getGroupID(), $script);
      $script = str_replace('LOCALISED_YES', ts("Yes", array('domain' => 'de.systopia.moregreetings')), $script);
      CRM_Core_Region::instance('page-header')->add(array(
        'script' => $script,
        ));
  }
}

/**
 * Hook implementation: Inject JS code into create/edit form
 */
function moregreetings_civicrm_buildForm($formName, &$form) {
  if ($formName == 'CRM_Contact_Form_Inline_CustomData') {
    if ($form->_groupID == CRM_Moregreetings_Config::getGroupID()) {
      // this is our form
      $script = file_get_contents(__DIR__ . '/js/render_moregreetings_edit.js');
      $script = str_replace('MOREGREETINGS', CRM_Moregreetings_Config::getGroupID(), $script);
      $script = str_replace('WRITE_PROTECTION_TS', ts("Write Protection", array('domain' => 'de.systopia.moregreetings')), $script);
      CRM_Core_Region::instance('page-footer')->add(array(
        'script' => $script,
        ));
    }
  } elseif ($formName == 'CRM_Contact_Form_Contact') {
    // this is our form
    $script = file_get_contents(__DIR__ . '/js/render_moregreetings_contactedit.js');
    $script = str_replace('MOREGREETINGS', CRM_Moregreetings_Config::getGroupID(), $script);
    $script = str_replace('WRITE_PROTECTION_TS', ts("Write Protection", array('domain' => 'de.systopia.moregreetings')), $script);
    CRM_Core_Region::instance('page-footer')->add(array(
      'script' => $script,
      ));
  }

}

/**
 * Hook implementation: update greetings on changes
 */
function moregreetings_civicrm_post($op, $objectName, $objectId, &$objectRef) {
  if ($op == 'edit' || $op == 'create') {
    if ($objectName == 'Individual' || $objectName == 'Organization' || $objectName == 'Household') {
      CRM_Moregreetings_Renderer::updateMoreGreetings($objectId);
    }
  }
}


/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function moregreetings_civicrm_enable() {
  _moregreetings_civix_civicrm_enable();

  require_once 'CRM/Utils/CustomData.php';
  $customData = new CRM_Utils_CustomData('de.systopia.moregreetings');
  $customData->syncCustomGroup(__DIR__ . '/resources/moregreetings_custom_group.json');
}

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function moregreetings_civicrm_config(&$config) {
  _moregreetings_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function moregreetings_civicrm_xmlMenu(&$files) {
  _moregreetings_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function moregreetings_civicrm_install() {
  _moregreetings_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function moregreetings_civicrm_postInstall() {
  _moregreetings_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function moregreetings_civicrm_uninstall() {
  _moregreetings_civix_civicrm_uninstall();
}



/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function moregreetings_civicrm_disable() {
  _moregreetings_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function moregreetings_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _moregreetings_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function moregreetings_civicrm_managed(&$entities) {
  _moregreetings_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function moregreetings_civicrm_caseTypes(&$caseTypes) {
  _moregreetings_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function moregreetings_civicrm_angularModules(&$angularModules) {
  _moregreetings_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function moregreetings_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _moregreetings_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function moregreetings_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function moregreetings_civicrm_navigationMenu(&$menu) {
  _moregreetings_civix_insert_navigation_menu($menu, NULL, array(
    'label' => ts('The Page', array('domain' => 'de.systopia.moregreetings')),
    'name' => 'the_page',
    'url' => 'civicrm/the-page',
    'permission' => 'access CiviReport,access CiviContribute',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _moregreetings_civix_navigationMenu($menu);
} // */
