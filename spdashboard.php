<?php

require_once 'spdashboard.civix.php';

/**
 * Implementation of hook_civicrm_config
 */
function spdashboard_civicrm_config(&$config) {
  _spdashboard_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 */
function spdashboard_civicrm_xmlMenu(&$files) {
  _spdashboard_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 */
function spdashboard_civicrm_install() {
  return _spdashboard_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 */
function spdashboard_civicrm_uninstall() {
  return _spdashboard_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 */
function spdashboard_civicrm_enable() {
  return _spdashboard_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 */
function spdashboard_civicrm_disable() {
  return _spdashboard_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 */
function spdashboard_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _spdashboard_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 */
function spdashboard_civicrm_managed(&$entities) {
  return _spdashboard_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 */
function spdashboard_civicrm_caseTypes(&$caseTypes) {
  _spdashboard_civix_civicrm_caseTypes($caseTypes);
}
