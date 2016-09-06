<?php

require_once 'spdashboard.civix.php';

function spdashboard_civicrm_dashboard_defaults($availableDashlets, &$defaultDashlets){
  $contactID = CRM_Core_Session::singleton()->get('userID');
	unset($defaultDashlets['getting-started']);
  unset($defaultDashlets['blog']);
  $defaultDashlets['spsearch'] = array(
    'dashboard_id' => $availableDashlets['spsearch']['id'],
    'is_active' => 1,
    'column_no' => '0',
    'contact_id' => $contactID,
  );
}

/**
 * Implementation of hook_civicrm_config
 */
function spdashboard_civicrm_config(&$config) {

	CRM_Core_Resources::singleton()
	                  ->addScriptFile('nl.sp.dashboard', 'js/searchdaslet.js', 1011, 'page-footer', FALSE);

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

	// Create Drupal news node type if necessary
	// -> now handled in Drupal module nl.sp.drupal-features

	// Add our dashlets to civicrm_dashboard table
	$dashlet_data = spdashboard_fetch_dashlets();

	if (!in_array('spnews', $dashlet_data)) {
		// Create news dashlet
		civicrm_api3('Dashboard', 'create', array(
			'name'           => 'spnews',
			'label'          => 'SP-nieuws',
			'url'            => 'civicrm/dashlet/spnews&reset=1&snippet=5',
			'permission'     => 'access CiviCRM',
			'column_no'      => 1,
			'is_minimized'   => 0,
			'fullscreen_url' => 'civicrm/dashlet/spnews&reset=1&snippet=5&context=dashletFullscreen',
			'is_fullscreen'  => 0,
			'is_active'      => 1,
			'is_reserved'    => 1,
		));
	}

	if (!in_array('spsearch', $dashlet_data)) {
		// Create search dashlet
		civicrm_api3('Dashboard', 'create', array(
			'name'           => 'spsearch',
			'label'          => 'Snelzoeken',
			'url'            => 'civicrm/dashlet/spsearch&reset=1&snippet=5',
			'permission'     => 'access CiviCRM',
			'column_no'      => 1,
			'is_minimized'   => 0,
			'fullscreen_url' => 'civicrm/dashlet/spsearch&reset=1&snippet=5&context=dashletFullscreen',
			'is_fullscreen'  => 0,
			'is_active'      => 1,
			'is_reserved'    => 1,
		));
	}

	// Parent
	return _spdashboard_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 */
function spdashboard_civicrm_uninstall() {

	// Remove our dashlets from civicrm_dashboard table
	$dashlet_data = spdashboard_fetch_dashlets();

	if (in_array('spnews', $dashlet_data)) {
		$id = array_search('spnews', $dashlet_data);
		civicrm_api3('Dashboard', 'delete', array('id' => $id));
	}

	if (in_array('spsearch', $dashlet_data)) {
		$id = array_search('spsearch', $dashlet_data);
		civicrm_api3('Dashboard', 'delete', array('id' => $id));
	}

	return _spdashboard_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 */
function spdashboard_civicrm_enable() {

	// Add widget to all users' dashboards
	$dashlet_data = spdashboard_fetch_dashlets();

	foreach ($dashlet_data as $dashlet_id => $dashlet_name) {

		if(!in_array($dashlet_name, array('spsearch', 'spnews')))
			continue;

		$column_no = ($dashlet_name == 'spsearch' ? 0 : 1);
		CRM_Core_DAO::executeQuery("UPDATE civicrm_dashboard_contact SET column_no = '{$column_no}', is_active = '1', weight = '1' WHERE dashboard_id = '{$dashlet_id}'");
	}

	return _spdashboard_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 */
function spdashboard_civicrm_disable() {

	// Remove widget from all users' dashboards
	$dashlet_data = spdashboard_fetch_dashlets();

	foreach ($dashlet_data as $dashlet_id => $dashlet_name) {

		if(!in_array($dashlet_name, array('spsearch', 'spnews')))
			continue;

		CRM_Core_DAO::executeQuery("UPDATE civicrm_dashboard_contact SET column_no = '0', is_active = '0', weight = '0' WHERE dashboard_id = '{$dashlet_id}'");
	}
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
function spdashboard_civicrm_upgrade($op, CRM_Queue_Queue $queue = null) {
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

/**
 * Function to fetch dashlets (used in the hooks above)
 */
function spdashboard_fetch_dashlets() {
	$data = civicrm_api3('Dashboard', 'get');
	if ($data['is_error'])
		throw new Exception("Could not initialize dashlets. API error: " . $data['error_message']);

	$dashlet_data = array();
	foreach ($data['values'] as $dashlet) {
		$dashlet_data[$dashlet['id']] = $dashlet['name'];
	}

	return $dashlet_data;
}