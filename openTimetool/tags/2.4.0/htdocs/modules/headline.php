<?php
/**
 * 
 * $Id$
 * 
 */

$noneProjectTasks = array();

if ($userAuth->isLoggedIn()) {
    // do only require those files when really needed, and that is when the user is logged in
    require_once $config->classPath . '/modules/project/treeDyn.php';
    require_once $config->classPath . '/modules/time/time.php';
    require_once $config->classPath . '/modules/task/task.php';
    $projectTreeDyn = modules_project_treeDyn::getInstance();

    $time->preset();
    $time->setWhere('user_id=' . $userAuth->getData('id'));
    $today = array_reverse($time->prepareResult($time->getDay()));

    if (sizeof($today)) {
        foreach ($today as $key => $aTime) {
            $_title = $dateTime->formatTimeShort($aTime['timestamp']);
            // AK : I hate php notes ;-) ...
            //$_title .= $aTime['duration'] ? " ({$aTime['duration']}) " : ' ';
            if (empty($aTime['duration'])) {
                $_title .= ' ';
            } else {
                $_title .= ' (' . $aTime['duration'] . ') ';
            }
            $_title .= $projectTreeDyn->getPathAsString($aTime['projectTree_id'])
                     . " - {$aTime['_task_name']}";
            $today[$key]['_title'] = $_title;
        }
        // AK : try to get the highest index for current task output
        $ct = sizeof($today) - 1;
        $currentTask = @$today[$ct];
    } else {
        $currentTask = null;
    }
    //print(sizeof($today)); echo '<p>';
    //var_dump($today); die();
    //$currentTask = @$today[0];
    $noneProjectTasks = $task->getNoneProjectTasks();
}

$isAdmin = $user->isAdmin();

require_once $config->finalizePage;
