<?php

/**
 * Class CRM_QuickSearchDashlet_Upgrader
 * Collection of upgrade steps.
 */
class CRM_QuickSearchDashlet_Upgrader extends CRM_QuickSearchDashlet_Upgrader_Base {

  /**
   * Add our dashlets to civicrm_dashboard table
   * @return bool Success
   */
  public function onEnable() {

    $dashlet_data = $this->getDashlets();

    if (!in_array('quicksearch', $dashlet_data)) {
      // Create search dashlet
      civicrm_api3('Dashboard', 'create', [
        'name'           => 'quicksearch',
        'label'          => 'Snelzoeken',
        'url'            => 'civicrm/dashlet/quicksearch?reset=1&snippet=5',
        'fullscreen_url' => 'civicrm/dashlet/quicksearch?reset=1&snippet=5&context=dashletFullscreen',
        'permission'     => 'access CiviCRM',
        'is_fullscreen'  => 0,
        'is_active'      => 1,
      ]);
    }

    return TRUE;
  }

  /**
   * emove our dashlets from civicrm_dashboard table
   * @return bool Success
   */
  public function onDisable() {

    $dashlet_data = $this->getDashlets();

    if (in_array('quicksearch', $dashlet_data)) {
      $id = array_search('quicksearch', $dashlet_data);
      civicrm_api3('Dashboard', 'delete', ['id' => $id]);
    }

    return TRUE;
  }

  /**
   * Function to fetch dashlets (used in the install/uninstall scripts above)
   * @return array Dashlets
   * @throws \Exception When dashlets could not be initialised
   */
  private function getDashlets() {
    $data = civicrm_api3('Dashboard', 'get');
    if ($data['is_error']) {
      throw new Exception("Could not initialise dashlets. API error: " . $data['error_message']);
    }

    $dashlet_data = [];
    foreach ($data['values'] as $dashlet) {
      $dashlet_data[$dashlet['id']] = $dashlet['name'];
    }
    return $dashlet_data;
  }

  /*
   * public function enable() { }
   * public function disable() { }
   *
   * By convention, functions that look like "function upgrade_NNNN()" are
   * upgrade tasks. They are executed in order (like Drupal's hook_update_N).
   *
   * public function upgrade_4200() {
   *   $this->ctx->log->info('Applying update 4200');
   *   $this->executeSqlFile('sql/upgrade_4200.sql');
   *   return TRUE;
   * }
   */
}
