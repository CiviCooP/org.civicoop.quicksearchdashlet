<?php

require_once 'quicksearchdashlet.civix.php';

/**
 * Implementation of hook_civicrm_config
 * @param $config array(string)
 */
function quicksearchdashlet_civicrm_config(&$config) {
	_quicksearchdashlet_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 * @param $files array(string)
 */
function quicksearchdashlet_civicrm_xmlMenu(&$files) {
	_quicksearchdashlet_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 * (Parent function executes Upgrader->install)
 */
function quicksearchdashlet_civicrm_install() {
  return _quicksearchdashlet_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 * (Parent function executes Upgrader->uninstall)
 */
function quicksearchdashlet_civicrm_uninstall() {
	return _quicksearchdashlet_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable
 */
function quicksearchdashlet_civicrm_enable() {
  return _quicksearchdashlet_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable
 */
function quicksearchdashlet_civicrm_disable() {
  return _quicksearchdashlet_civix_civicrm_disable();
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
function quicksearchdashlet_civicrm_upgrade($op, CRM_Queue_Queue $queue = null) {
	return _quicksearchdashlet_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 */
function quicksearchdashlet_civicrm_managed(&$entities) {
	return _quicksearchdashlet_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 */
function quicksearchdashlet_civicrm_caseTypes(&$caseTypes) {
	_quicksearchdashlet_civix_civicrm_caseTypes($caseTypes);
}

function quicksearchdashlet_civicrm_dashboard_defaults($availableDashlets, &$defaultDashlets){
  $contactID = CRM_Core_Session::singleton()->get('userID');
  $defaultDashlets['quicksearch'] = array(
    'dashboard_id' => $availableDashlets['quicksearch']['id'],
    'is_active' => 1,
    'column_no' => '0',
    'contact_id' => $contactID,
  );
}

/**
 * Function to fetch dashlets (used in the hooks above)
 */
function quicksearchdashlet_fetch_dashlets() {
	$data = civicrm_api3('Dashboard', 'get');
	if ($data['is_error'])
		throw new Exception("Could not initialize dashlets. API error: " . $data['error_message']);

	$dashlet_data = array();
	foreach ($data['values'] as $dashlet) {
		$dashlet_data[$dashlet['id']] = $dashlet['name'];
	}

	return $dashlet_data;
}
