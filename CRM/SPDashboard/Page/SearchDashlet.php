<?php

require_once 'CRM/Core/Page.php';

class CRM_SPDashboard_Page_SearchDashlet extends CRM_Core_Page {

	function run() {
		CRM_Utils_System::setTitle(ts('SearchDashlet'));
		parent::run();
	}
}
