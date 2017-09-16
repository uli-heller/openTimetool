<?php
/**
 * 
 * $Id$
 * 
 * This creates the initial page after successful login and is called by selecting
 * "today" from the menu on the left as well 
 * One of the main pages of openTimeTool !
 * 
 */

require_once '../../../config.php';

/**
 * Check if we are here by erraneous redirect from opera mini and such
 */
require_once $config->classPath . '/mobile_browser.php';
$browser = $_SERVER['HTTP_USER_AGENT'];
if (is_mobile($browser)) {
    /**
     * well we should be in mobile.php but got somehow redirected to
     * today.php (this one) on some opera mini browsers on some mobiles on some servers
     */
    $thisuri = $_SERVER['REQUEST_URI'];
    $thisuri = str_ireplace('today.php', 'mobile.php', $thisuri);
    $host = $_SERVER['HTTP_HOST'];
    $prot = 'http' . ($_SERVER['HTTPS'] != 'on'?'':'s');
    header("Location: $prot://$host/$thisuri");
    exit;        
}

if (!$config->isLiveMode()) {
//    include_once $config->applRoot . '/logging.php';
//    $logging->_logme('today', 'start');
}

require_once $config->classPath . '/pageHandler.php';
require_once $config->classPath . '/modules/time/time.php';
require_once $config->classPath . '/modules/task/task.php';
require_once $config->classPath . '/modules/user/user.php';
require_once $config->classPath . '/modules/project/treeDyn.php';
require_once $config->classPath . '/modules/project/member.php';

if (!$config->isLiveMode()) {    
//    include_once $config->applRoot . '/logging.php';
//    $logging->_logme('today', 'projectMember : ' . print_r($projectMember, true)); 
}

$userId = $userAuth->getData('id');
// we do only log for the current user here!
$_REQUEST['newData']['user_id'] = $userId;

$projectTree = modules_project_tree::getInstance(true);

// those two lines handle the edit functionality
$pageHandler->setObject($time);     

$had_a_non_project_task = false;
if (isset($_REQUEST['newData']['projectTree_id']) &&
        empty($_REQUEST['newData']['projectTree_id'])) {
    // We have new data from form and want to save it below
    $ourtask = $_REQUEST['newData']['task_id'];
    $ourproject = @$_REQUEST['newData']['projectTree_id'];
    if ($task->isNoneProjectTask($ourtask)) {
        // 2.3.0 SX (AK) : we use the last used projectid if not set in form and task
        // is a task without project
        // similar to shortcut.php
        $time->reset();
        $time->setSelect('projectTree_id');
        $time->setWhere('user_id=' . $userAuth->getData('id'));
        $time->setOrder('timestamp', true);
        $lastTime = $time->getAll(0, 1);
        $projectId = $lastTime[0]['projectTree_id'];
        // check if the project is available, if not use root-id
        $projectTree = modules_project_tree::getInstance(true);
        if (!$projectId || !$projectTree->isAvailable($projectId , time())) {
            if (sizeof($availableProjects = $projectTree->getAllAvailable())) {
                foreach ($availableProjects as $aProject) {
                    if ($projectMember->isMember( $aProject['id'])) {
                        $projectId = $aProject['id'];
                        break;
                    }
                }
            }
        }
        // the default project if any
        $ourproject = $projectId;
        $had_a_non_project_task = true;
    }
    $_REQUEST['newData']['projectTree_id'] = $ourproject;
}

$saved = $pageHandler->save($_REQUEST['newData']);
$data = $pageHandler->getData();

if ($had_a_non_project_task) {
    // delete the project info for this non project booking again
    // to not confuse the user
    $data['projectTree_id'] = 0;
}

// convert the time and date, so the macro can show it properly ... do this better somehow
if (@$data['timestamp_date'] && @$data['timestamp_time'] && !@$data['timestamp']) {
    $_date = explode('.', $data['timestamp_date']);
    $_time = explode(':', $data['timestamp_time']);
    $data['timestamp'] = mktime(
            $_time[0], $_time[1], 0, $_date[1], $_date[0], $_date[2]);
}

if ($saved) {
    unset($data['id']);
    unset($data['comment']);
}

// this handles the remove-functionality
if (isset($_REQUEST['removeId'])) {
    $time->remove($_REQUEST['removeId']);
}

$curUserId = $userId;
$isAdmin = $user->isAdmin();

$time->preset();
$time->addWhere("user_id=$userId");
$times = $time->getDay();
$tasks = $task->getAll();
$projectTreeJsFile = 'projectTree' . ($isAdmin?'Admin':'');

require_once $config->finalizePage;
