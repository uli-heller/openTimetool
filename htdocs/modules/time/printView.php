<?php
/**
 * 
 * $Id$
 * 
 */

require_once '../../../config.php';

require_once $config->classPath . '/modules/project/tree.php';
require_once $config->classPath . '/modules/time/time.php';

$show = $session->temp->time_index;

if ($show['projectTree_ids']) {
    $time->addWhere('projectTree.id IN (' . implode(',', $show['projectTree_ids']) . ')');
}
if (sizeof($show['task_ids'])) {
    $time->addWhere('task.id IN (' . implode(',', $show['task_ids']) . ')');
}
if ($show['user_ids']) {
    $time->addWhere('user.id IN (' . implode(',', $show['user_ids']) . ')');
}
//$time->setWhere('user_id=' . $userAuth->getData('id'));
//$times = $time->getDay();

$times = $time->getDay($show['dateFrom'], $show['dateUntil']);

$showCols['task'] = (!$_REQUEST['cols'] || ($_REQUEST['cols'] && $_REQUEST['cols']['task']));
$showCols['start'] = (!$_REQUEST['cols'] || ($_REQUEST['cols'] && $_REQUEST['cols']['start']));
$showCols['duration'] = (!$_REQUEST['cols'] || ($_REQUEST['cols'] && $_REQUEST['cols']['duration']));
$showCols['comment'] = (!$_REQUEST['cols'] || ($_REQUEST['cols'] && $_REQUEST['cols']['comment']));
$showCols['project'] = (!$_REQUEST['cols'] || ($_REQUEST['cols'] && $_REQUEST['cols']['project']));

if (sizeof($show['projectTree_ids']) == 1) {
    $showCols['project'] = false;
}

$numCols = 0;
foreach ($showCols as $aCol) {
    if ($aCol) {
        $numCols++;
    }
}

$layout->setMainLayout('/modules/dialog');

require_once $config->finalizePage;
