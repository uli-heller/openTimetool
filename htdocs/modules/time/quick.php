<?php
/**
 * 
 * $Id$
 * 
 */

require_once("../../../config.php");

require_once $config->classPath . '/pageHandler.php';
require_once $config->classPath . '/modules/time/time.php';
require_once $config->classPath . '/modules/task/task.php';
require_once $config->classPath . '/modules/project/treeDyn.php';

if (isset($_REQUEST['quickLog'])) {
    if (!$aTime = $time->get($_REQUEST['quickLog'])) {
        $applError->set('Sorry, but the Entry you chose was removed meanwhile. Log failed!');
    } else {
        unset($aTime['id']);
        $aTime['timestamp'] = time();
        $aTime['durationSec'] = 0;
// FIXXME check if the string _ends_ with the string!!!!
        if (strpos($aTime['comment'], '[Quick-Log]') === false) {
            $aTime['comment'] .= ' [Quick-Log]';
        }
        $_REQUEST['newData'] = $aTime;
        $_REQUEST['action_saveAsNew'] = true;
    }
}

$pageHandler->setObject($time);
// AK : use @ as if empty a null handler is returned
$data = $pageHandler->saveHandler(@$_REQUEST['newData']);

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
$projectTreeJsFile = 'projectTree' . ($isAdmin?'Admin':'');

$layout->setMainLayout('/modules/dialog');

require_once $config->finalizePage;
