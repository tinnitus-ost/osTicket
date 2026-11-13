<?php

/*
 * Overview of the installed applications (plugins), shown when agents click
 * on "Applications" in the agent or admin navigation. Redesigned overview:
 * cards per panel, see include/staff/apps.inc.php.
 */

// Anpassung Anfang: Apps - admin panel uses admin navigation
// From the admin panel (apps.php?admin): admin navigation with the admin
// applications in the sub menu; admin.inc.php redirects non-admins.
if (isset($_GET['admin']))
    require('admin.inc.php');
else
    require('staff.inc.php');
// Anpassung Ende: Apps - admin panel uses admin navigation
require_once INCLUDE_DIR.'class.plugin.php';
require_once INCLUDE_DIR.'class.app.php';

$nav->setActiveTab('apps', -1);

require(STAFFINC_DIR.'header.inc.php');

// Sections of the overview: the applications of the current panel first,
// admins see the applications of the other panel below. Collected after
// the header: plugins may register applications in the signal header.scp
// (the agent is not known yet when plugins are bootstrapped).
$adminPanel = isset($_GET['admin']);
$staffapps = Application::getStaffApps() ?: array();
$adminapps = $thisstaff->isAdmin() ? (Application::getAdminApps() ?: array()) : null;
$appSections = array();
$appSections['staff'] = array('title' => __('Staff'), 'apps' => $staffapps);
if (is_array($adminapps))
    $appSections['admin'] = array('title' => __('Admin'), 'apps' => $adminapps);
if ($adminPanel && isset($appSections['admin']))
    $appSections = array('admin' => $appSections['admin']) + $appSections;

require(STAFFINC_DIR.'apps.inc.php');
include(STAFFINC_DIR.'footer.inc.php');
