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
require_once $config->classPath . '/modules/user/user.php';
require_once $config->classPath . '/modules/project/treeDyn.php';
require_once $config->classPath . '/modules/project/member.php';

$doEdit = false;
if (!empty($_REQUEST['id'])) {
    $doEdit = true;
    // get the timestamp that belongs to this id
    $time->reset();
    $time->setSelect('timestamp');
    $curData = $time->get($_REQUEST['id']);
    // set the from and until
    $fromDay = $curData['timestamp'];
    $untilDay = $curData['timestamp'];
} else {
    $fromDay = strtotime('last ' . date('l'));
    $untilDay = time();
}

$isAdmin = $user->isAdmin();
$userId = $userAuth->getData('id');

// those two lines handle the edit functionality
$time->preset();
$pageHandler->setObject($time);
// AK : if newData not in REQUEST, save returns an empty object
$saved = $pageHandler->save(@$_REQUEST['newData']);

$data = $pageHandler->getData();
/*
// convert the time and date, so the macro can show it properly ... do this better somehow
if(@$data['timestamp_date'] && @$data['timestamp_time'] && !$data['timestamp']) {
    $_date = explode('.', $data['timestamp_date']);
    $_time = explode(':', $data['timestamp_time']);
    $data['timestamp'] = mktime($_time[0], $_time[1], 0, $_date[1], $_date[0], $_date[2]);
}

if ($saved) {
    unset($data['id']);
    unset($data['comment']);
}

// this handles the remove-functionality
if (isset($_REQUEST['removeId'])) {
    $time->remove($_REQUEST['removeId']);
}
*/

$time->preset();
$time->addWhere("user_id=$userId");
$times = $time->getDay($fromDay, $untilDay);
$_times = array_reverse($times);
$projectTreeJsFile = 'projectTree' . ($isAdmin?'Admin':'');

$times = array();
$colorLegend = array();
if (sizeof($_times)) {
    $dayName = null;
    $cnt = 0;
    foreach ($_times as $key => $aTime) {
        if ($dayName != date('l', $aTime['timestamp'])) {
            // if the last task of the day has no duration then we set an additional prop
            // since it is 'Gehen' or something
            if (isset($times[$cnt]) &&
                    !isset($times[$cnt][sizeof($times[$cnt])-1]['duration'])) {
                $times[$cnt][sizeof($times[$cnt])-1]['_endOfDay'] = true;
            }

            $midnite = mktime(0, 0, 0, date('m', $aTime['timestamp']),
                    date('d', $aTime['timestamp']), date('Y', $aTime['timestamp']));
            $_data['id'] = 0;
            $_data['durationSec'] = $aTime['timestamp'] - $midnite;
            $_data['_task_color'] = 'white';
            $cnt++;
            $times[$cnt][] = $_data;
        }

        $dayName = date('l', $aTime['timestamp']);

        $_title  = $dateTime->formatTimeShort($aTime['timestamp']);
        $_title .= isset($aTime['duration']) ? " ({$aTime['duration']}) " : ' ';
        $_title .= $projectTreeDyn->getPathAsString($aTime['projectTree_id']);
        $_title .= " - {$aTime['_task_name']}";
        $aTime['_title'] = $_title;
        $times[$cnt][] = $aTime;

        $colorLegend[strtolower($aTime['_task_name'])] = array(
            'color' => $aTime['_task_color'],
            'name'  => $aTime['_task_name'],
        );
    }

    // this is only for the very last entry!!!
    // if the last task of the day has no duration then we set an additional prop
    // since it is 'Gehen' or something
    if (isset($times[$cnt]) &&
            !isset($times[$cnt][sizeof($times[$cnt])-1]['duration'])) {
        $times[$cnt][sizeof($times[$cnt])-1]['_endOfDay'] = true;
    }
}

ksort($colorLegend);
$zoomFactor = 3;
$oneHourWidth = $time->getImgWidth(3600, $zoomFactor);

$myFormat = $dateTime->setFormat('D, d.m.y');
$tasks = $task->getAll();

require_once $config->finalizePage;
