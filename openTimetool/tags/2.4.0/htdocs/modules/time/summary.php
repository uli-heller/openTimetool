<?php
/**
 * 
 * $Id$
 * 
 */

require_once '../../../config.php';

require_once $config->classPath . '/modules/time/time.php';
require_once $config->classPath . '/modules/task/task.php';
//require_once $config->classPath . '/modules/user/user.php';
require_once $config->classPath . '/modules/project/tree.php';

/* i was trying this with phpmyadmin, problem is only that i dont get the join properly done
SELECT *
FROM timet
LEFT OUTER JOIN pricep ON ( p.projectTree_id = t.projectTree_id ) OR (
p.projectTree_id = 0
)
WHERE (
p.validFrom < t.timestamp AND (
p.validUntil = 0 OR p.validUntil > t.timestamp
)
) AND t.task_id = p.task_id
*/

/*
$times = $time->execute(
    'SELECT projectTree_id,projectTree.name as project,task.name as task,SUM(durationSec) as total ' .
    'FROM time,task,projectTree ' .
    'WHERE projectTree_id=projectTree.id AND task_id=task.id AND task.calcTime=1 ' .
    'GROUP BY projectTree_id,task_id ORDER BY projectTree_id,task'
);
*/
$times = $time->execute(
    'SELECT time.projectTree_id,projectTree.name AS project, task.name AS task,' .
    'SUM(durationSec) AS totalTime,' .
    'SUM(durationSec)/(60*60)*price.internal AS totalInternal,' .
    'SUM(durationSec)/(60*60)*price.external AS totalExternal ' .
    'FROM time,task,projectTree,price ' .
    'WHERE time.projectTree_id=projectTree.id AND time.task_id=task.id AND task.calcTime=1 ' .
    'AND price.task_id=task.id ' .
    'GROUP BY time.projectTree_id,time.task_id ORDER BY time.projectTree_id,task'
);
// collect the keys of the times and match them to the project they belong to
// so we can access the times via the project id in the tpl
$timesKeys = array();
foreach ($times as $key => $aTime) {
    $timesKeys[$aTime['projectTree_id']][] = $key;
}

$projectTree = modules_project_tree::getInstance(true);
$projects = $projectTree->getAllVisible();

require_once $config->finalizePage;
