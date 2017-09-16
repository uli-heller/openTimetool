<?php
/**
 * 
 * $Id$
 * 
 */

require_once '../../../config.php';

require_once $config->classPath . '/pageHandler.php';
require_once $config->classPath . '/modules/time/time.php';
require_once $config->classPath . '/modules/task/task.php';
//require_once $config->classPath . '/modules/project/treeDyn.php';

/**
 * We don't use dynTree as this needs Jscript
 */
require_once 'vp/Application/HTML/Tree.php';
require_once $config->classPath . '/modules/project/tree.php';

$projectTree = modules_project_tree::getInstance();    
$allFolders = $projectTree->getAllAvailable();    

$pageHandler->setObject($time);
// AK : use @ as if empty a null handler is returned
$data = $pageHandler->saveHandler(@$_REQUEST['newData']);

// we set the time/date to the current datetime when page is redrwan
$data['timestamp'] = mktime(
        date('H'), date('i'), date('s'), date('m'), date('d'), date('Y'));

$tasks = $task->getAll();

//$time->setGroup('task_id');
$time->preset();
$time->setWhere('user_id=' . $userAuth->getData('id'));
//$time->setGroup('task_id,projectTree_id');
$time->setOrder('timestamp', true);
$lastTimes = $time->getAll(0, 5);
//print_r($lastTimes);

$lastTime = @$lastTimes[0];
$isAdmin = $user->isAdmin();
// we use the static tree version; see above
$projectTreeJsFile = 'projectTree' . ($isAdmin?'Admin':'');

// to get the page with all that stuff around
$layout->setMainLayout('/modules/dialog');

require_once $config->finalizePage;
