<?php
/**
 * openTimetool
 *
 * CopyRight : system worx GmbH&Co KG
 * All Rights Reserved
 *
 * Last Change:
 *    $Revision: 272 $
 *    $Author: akejr $
 *    $Date: 2012-06-04 20:33:14 +0200 (Mo, 04. Jun 2012) $
 *    $Id: ajax_index_if.php 272 2012-06-04 18:33:14Z akejr $
 *
 * Created on Mar 13, 2008
 *
 * Xajax interface include
 *
 */


/**
 * Das ganze Ajax-Zeugs ....
 *
 */

/**
 * includes
 */
require_once($config->classPath.'/modules/time/time.php');
require_once($config->classPath.'/modules/task/task.php');
require_once($config->classPath.'/modules/project/tree.php');
require_once $config->classPath.'/modules/project/treeDyn.php';
require_once $config->classPath.'/modules/project/member.php';


/**
 * Just an interface to docheckBookings below
 */
function checkBookings($projectTreeId,$taskId,$oldId,$bookDate,$bookTime,$calledfrom='today')
{
	$objResponse = new xajaxResponse();
	
	$ret = docheckBookings($objResponse,$projectTreeId,$taskId,$oldId,$bookDate,$bookTime,$calledfrom);
	
	$objResponse = $ret->objResponse;
	foreach($ret->htmlretarr as $name=>$val) {
		$objResponse->assign($name,"value",$val);
	}

	return $objResponse;
}

/**
 * Just an interface to docheckBookings below
 * Input params are all fields
 */
function checkmultipleBookings($projectTreeId,$taskId,$oldId,$bookDate,$bookTime,$calledfrom='today')
{
	$objResponse = new xajaxResponse();
	
	foreach($projectTreeId as $index=>$pid) {
		// now we call each booking and break if one is overbooked
		$ret = docheckBookings($objResponse,$projectTreeId[$index],$taskId[$index],0,$bookDate[$index],$bookTime[$index],$calledfrom);
		$objResponse = $ret->objResponse;
		if($ret->htmlretarr['overBooked'] > 0) {
			// we have an overbooking here
			foreach($ret->htmlretarr as $name=>$val) {
				$objResponse->assign($name,"value",$val);
			}
			break;  // outa here
		}
	}

	return $objResponse;
}



/**
 * checkData
 *
 * Called during booking/update on today and other time pages
 * We check if we have overbooked and set a variable
 * data in hidden field on form. In the submit JS-Function we have then the
 * checking if the project is overbooked
 * 
 * calledfrom: parameter if we have to do special things
 * 
 * As we are called now from 2 xajax functions, we return a structure and populate the html fields 
 * in the 2 xajax functions.
 * 
 */
function docheckBookings($objResponse,$projectTreeId,$taskId,$oldId,$bookDate,$bookTime,$calledfrom)
{
	global $userAuth;
	global $task,$time;

	define('XAJAXDBG',false);

	$ret = new stdClass;
	$ret->htmlretarr = array();  // key = name, value = value
	$ret->objResponse;

	$userid = $userAuth->getData('id');

	$overbooked = false;
	$rest=$hrest=0;
	$laststamp = '';
	
	if($calledfrom == 'short') {
		$prefback = 'short';
	} else {
		$prefback='';
	}

	if(XAJAXDBG) $objResponse->assign("msgxajax","innerHTML","checkBookings<br>");

	$projectTree =& modules_project_tree::getInstance(true);  // base of that all


	// get the projectId even if we don't have one yet ;-) (non project task)
	$projectTreeId = getProjectId($projectTree,$projectTreeId,$taskId,$userId);

	//if(!empty($projectTreeId) && $task->isNoneProjectTask($taskId)) {  // only if we DONT have s start task
	if(!empty($projectTreeId)) {

		if(XAJAXDBG) {
			if($task->isNoneProjectTask($taskId)) $objResponse->append("msgxajax","innerHTML","Nonprojecttask<br>");
			else $objResponse->append("msgxajax","innerHTML","Projecttask<br>");

		}


		$project = $projectTree->getElement($projectTreeId);

		$maxDuration = $project['maxDuration']*3600;
		$startDate = $project['startDate'];
		$endDate = $project['endDate'];
		$projectName = $project['name'];

		// check only if there is a max duration
		if(empty($maxDuration)) {
			/*
			$objResponse->assign($prefback."restAvailable","value",$hrest);
			$objResponse->assign($prefback."overBooked","value",$overbooked);
			return $objResponse;
			*/
			$ret->htmlretarr = array($prefback."restAvailable"=>$hrest, $prefback."overBooked"=>$overbooked);
			$ret->objResponse = $objResponse;
			return $ret;
		}

		// the timestamp selected on our form
		$bdate = explode('.',$bookDate);
		$btime = explode(':',$bookTime);
		$timestamp = mktime($btime[0],$btime[1],0,$bdate[1],$bdate[0],$bdate[2]);

		if(XAJAXDBG) $objResponse->append("msgxajax","innerHTML","OUR stamp: ".$timestamp.' ('.date('r',$timestamp).')<br>');

			
		$btimes = AllBookedTimes($projectTreeId);

		$currsum = $beforesum = SumBookedTimes($btimes);

		if(XAJAXDBG) $objResponse->append("msgxajax","innerHTML","Booked sum up to now: ".$currsum.'<br>');

		if(empty($oldId)) {
			$newData = array();
			$newData['timestamp'] = $timestamp;
			$newData['user_id'] = $userid;
			$newData['task_id'] = $taskId;
			$newData['projecTree_id'] = $projectTreeId;
			$newData['durationSec'] = 0;
			$hi= 0;
			foreach($btimes as $b) {
				if($b['id']>$hi) $hi = $b['id'];
			}
			$newData['id'] = $hi+1;
			$btimes[$newData['timestamp']] = $newData;
		}

		// do we have an add in between or do we append next timestamp
		if(($nextstampo = $time->_getNextTimestampProject($timestamp,$projectTreeId)) !== false) {
			if(XAJAXDBG) $objResponse->append("msgxajax","innerHTML",'Add or update in between<br>');
		} else {
			if(XAJAXDBG) $objResponse->append("msgxajax","innerHTML",'Append new one<br>');
		}

		if(XAJAXDBG) $objResponse->append("msgxajax","innerHTML",'btimes: '.print_r($btimes,true).'<p>');


		if(!empty($oldId)) {
			foreach($btimes as $b) {
				if($b['id'] == $oldId) {
					$oldData = $b;
					break;
				}
			}
			reset($btimes);
		}

		if(XAJAXDBG) $objResponse->append("msgxajax","innerHTML",'oldData:('.$oldId.') '.print_r($oldData,true).'<br>');


		krsort($btimes);

		if (!empty($oldId)) {
			$btimes = updateDuration(XAJAXDBG,$btimes, $oldData,$objResponse ); // first update the entries around the old time
			$btimes = updateDuration(XAJAXDBG,$btimes, $oldId,$objResponse ); // update the entries around the updated time
		} else {
			// new stamp just sort it in
			$btimes = updateDuration(XAJAXDBG,$btimes,$newData,$objResponse);
		}

		if(XAJAXDBG) $objResponse->append("msgxajax","innerHTML",'updated btimes: '.print_r($btimes,true).'<p>');

		$currsum = SumBookedTimes($btimes);

		if(XAJAXDBG) $objResponse->append("msgxajax","innerHTML","Booked sum after update: ".$currsum.'<br>');


		$rest = $maxDuration - $currsum;
		$wouldbe = $beforesum + ($beforesum - $currsum);
		if(XAJAXDBG) $objResponse->append("msgxajax","innerHTML","Would have after booking: ".$wouldbe.'('.$time->_calcDuration($wouldbe,'hour').')<br>');

		if($rest < 0 || ($rest-$wouldbe)<0) {
			$overbooked = true;
		}

		if($rest < 0) {
			$hrest = '-'.$time->_calcDuration(abs($rest),'hour');
		} else {
			$hrest = $time->_calcDuration($rest,'hour');
		}

		if(XAJAXDBG) $objResponse->append("msgxajax","innerHTML","Rest: ".$rest.'('.$hrest.')<br>');

		unset($btimes); unset($projectTree);

	}

	/*
	$objResponse->assign($prefback."restAvailable","value",$hrest);
	$objResponse->assign($prefback."overBooked","value",$overbooked);
		
	return $objResponse;
	*/
	
	$ret->htmlretarr = array($prefback."restAvailable"=>$hrest, $prefback."overBooked"=>$overbooked);
	$ret->objResponse = $objResponse;
	return $ret;
	
}

/**
 * Simulate the method in class time without DB
 */
function updateDuration($dbg, $btimes, $data, & $objResponse)
{
	global $time;

	if($dbg) $objResponse->append("msgxajax","innerHTML","In updateduration: ".print_r($data,true).'<br>');
	if(XAJAXDBG) $objResponse->append("msgxajax","innerHTML",'<br>updatedDuration btimes0: '.print_r($btimes,true).'<p>');

	if (is_numeric($data)) {
		foreach($btimes as $b) {
			if($dbg) $objResponse->append("msgxajax","innerHTML","get rec ".$b['id']."<br>");
				
			if($b['id'] == $data) {
				$data = $b;
				break;
			}
		}
		reset($btimes);
		if($dbg) $objResponse->append("msgxajax","innerHTML","update: ".print_r($data,true).'<br>');

	}

	//$rows = get3PrevTimestamps($dbg,$btimes,$data['timestamp'],$data['user_id'], $objResponse);

	if(XAJAXDBG) $objResponse->append("msgxajax","innerHTML",'<br>updatedDuration btimes1: '.print_r($btimes,true).'<p>');


	$rows = array();
	while ($d = current($btimes) )
	{
		if($dbg) $objResponse->append("msgxajax","innerHTML","prevloop ".print_r($d,true)."<br>");
		if($d['user_id'] != $data['user_id']) {
			$d = next($btimes);
			continue;
		}
		if($d['timestamp'] == $data['timestamp']) {
			if($dbg) $objResponse->append("msgxajax","innerHTML","found starting prev with id ".$d['id']."<br>");
			$n = next($btimes);
			if(empty($n)) {
				// no previous stamp : booking before last start
				$next = prev($btimes);
				if(!empty($next)) {
				 $btimes[$d['timestamp']]['durationSec'] = $next['timestamp']-$d['timestamp'];
				}
				break;
			}
			$p = prev($btimes);
			if(!empty($p)) $rows[]=$p;
			$p = prev($btimes);
			if(!empty($p)) $rows[]=$p;
			break;
		}
		$d = next($btimes);
	}

	if(XAJAXDBG) $objResponse->append("msgxajax","innerHTML",'<br>updatedDuration btimes2: '.print_r($btimes,true).'<p>');


	if($dbg) $objResponse->append("msgxajax","innerHTML","prev3: ".print_r($rows,true).'<br>');

	for ($i=1; $i<sizeof($rows); $i++) {
		$ts = $rows[$i-1]['timestamp'];
		// round the time before updating, this depends on the project it belongs to
		$durationSec = $time->roundTime($rows[$i]['timestamp']-$rows[$i-1]['timestamp'],$rows[$i-1]['projectTree_id']);
		$btimes[$ts]['durationSec'] = $durationSec;
	}

	if(XAJAXDBG) $objResponse->append("msgxajax","innerHTML",'<br>updatedDuration btimes3: '.print_r($btimes,true).'<p>');

	return $btimes;
}




/**
 * Get the project id of our booking
 * See today.php and quick.php how we get the id of
 * non project tasks where there is no project explicitely selected
 *
 * @param object $projectTree
 * @param int $projectId
 * @param int $taskId
 * @param int $userId
 */
function getProjectId($projectTree, $projectId,$taskId,$userId)
{
	global $userAuth;
	global $task,$time,$projectMember;

	$had_a_non_project_task = false;
	if(empty($projectId)) {
		// We have new data from form and want to save it below
		$ourtask = $taskId;
		$ourproject = $projectId;
		if($task->isNoneProjectTask($ourtask)) {
			// 2.3.0 SX (AK) : we use the last used projectid if not set in form and task
			// is a task without project similar to shortcut.php
			$time->reset();
			$time->setSelect('projectTree_id');
			$time->setWhere('user_id='.$userId);
			$time->setOrder('timestamp',true);
			$lastTime = $time->getAll(0,1);
			$projectId = $lastTime[0]['projectTree_id'];
			// check if the project is available, if not use root-id
			if( !$projectId || !$projectTree->isAvailable( $projectId , time() ) )
			{
				if( sizeof($availableProjects = $projectTree->getAllAvailable()) )
				{
					foreach( $availableProjects as $aProject )
					{
						if( $projectMember->isMember( $aProject['id'] ) )
						{
							$projectId = $aProject['id'];
							break;
						}
					}
				}
			}
			$ourproject = $projectId;  // the default project if any
			$had_a_non_project_task = true;
		}
		$projectId = $ourproject;
	}

	return $projectId;
}

/**
 * Get all booked times on given project
 *
 * Ak, system worx : We need that for check of overbooking
 */
function AllBookedTimes($dataID)
{
	global $projectTree;
	global $task;

	$ret = array();

	$time = new modules_common(TABLE_TIME);
	$time->reset();
	$time->setWhere('projectTree_id='.$dataID);
	$time->setOrder('timestamp',true);
	if ($time->getCount()) {
		// AK, system worx : retrieve all logged times for that project
		$result = $time->GetAll();
		foreach($result as $data) {
			$ret[$data['timestamp']] = $data;
		}
	}
	unset($time);

	return $ret;
}


/**
 * Get a sum of all booked times on given project
 *
 * Ak, system worx : We need that for check of overbooking
 */
function SumBookedTimes($bookedTimes)
{
	global $projectTree;
	global $task;

	$sum = 0;

	foreach($bookedTimes as $data) {
		//echo "times ".print_r($data,true).'<br>';
		if(!$task->isNoneProjectTask($data['task_id'])) $sum += $data['durationSec'];
	}

	return $sum;
}


// Instantiate the xajax object.  No parameters defaults requestURI to this page, method to POST, and debug to off
$xajax = new xajax();


$xajax->configure("statusMessages", true);
if(strtoupper(trim(CHARSET)) != 'UTF-8' )
{
	$xajax->configure("decodeUTF8Input", false);
}
//$xajax->configure("debug",true); // Uncomment this line to turn debugging on


// Specify the PHP functions to wrap. The JavaScript wrappers will be named xajax_functionname
$xajax->registerFunction("checkBookings");
$xajax->registerFunction("checkmultipleBookings");


// Process any requests.  Because our requestURI is the same as our html page,
// this must be called before any headers or HTML output have been sent
$xajax->processRequest();


header("Content-Type: text/html; charset=".$xajax->sEncoding);
?>