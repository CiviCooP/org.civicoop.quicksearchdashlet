<?php

require_once 'CRM/Core/Page.php';

class CRM_QuickSearchDashlet_Page_QuickSearch extends CRM_Core_Page {

  function run() {

    $res = CRM_Core_Resources::singleton();
    $res->addScriptFile('org.civicoop.quicksearchdashlet', 'assets/quicksearch.js', 1011);
    $res->addStyleFile('org.civicoop.quicksearchdashlet', 'assets/quicksearch.css', 1011);

    CRM_Utils_System::setTitle(ts('QuickSearchDashlet'));
    parent::run();
  }
}
