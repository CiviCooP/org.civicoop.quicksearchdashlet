<?php

require_once 'CRM/Core/Page.php';

class CRM_SPDashboard_Page_NewsDashlet extends CRM_Core_Page {
	function run() {

		// Get latest news items * DRUPAL ONLY
		$result = new EntityFieldQuery();
		$result
			->entityCondition('entity_type', 'node')
			->propertyCondition('type', 'news')
			->propertyCondition('status', NODE_PUBLISHED)
			->propertyOrderBy('changed', 'DESC')
			->execute();

		$node_ids = array();
		foreach($result->ordered_results as $result)
			$node_ids[] = $result->entity_id;
		$nodes = node_load_multiple($node_ids);

		// Render view
		CRM_Utils_System::setTitle(ts('NewsDashlet'));
		self::$_template->assign('items', $nodes);
		parent::run();
	}
}
