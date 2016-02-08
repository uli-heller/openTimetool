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

define('XAJAXDBG',false);


/**
 * Just an interface to docheckBookings below
 * covering today,index,shortcut and quick log
 */
function checkBookings($projectTreeId,$taskId,$oldId,$bookDate,$bookTime,$calledfrom='today')
{
	$objResponse = new xajaxResponse();

	if(XAJAXDBG) 	$objResponse->assign("msgxajax","innerHTML","checkBookings<br>");

	$ret = docheckBookings($objResponse,$projectTreeId,$taskId,$oldId,$bookDate,$bookTime,$calledfrom);

	$objResponse = $ret->objResponse;
	foreach($ret->htmlretarr as $name=>$val) {
		$objResponse->assign($name,"value",$val);
	}

	return $objResponse;
}

/**
 * Just an interface to docheckBookings below
 * Input params are all fields and we come from multi log
 */
function checkmultipleBookings($projectTreeId,$taskId,$oldId,$bookDate,$bookTime,$calledfrom='today')
{
	$objResponse = new xajaxResponse();

	if(XAJAXDBG) 	$objResponse->assign("msgxajax","innerHTML","checkmultipleBookings<br>");

	
	$btimesarr = array();
	foreach($projectTreeId as $index=>$pid) {
		// now we call each booking and break if one is overbooked
		if(!isset($btimesarr[$projectTreeId[$index]])) $btimesarr[$projectTreeId[$index]] = null;
		
		$ret = docheckBookings($objResponse,$projectTreeId[$index],$taskId[$index],0,$bookDate[$index],$bookTime[$index],'period',$btimesarr[$projectTreeId[$index]]);
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
 * Just an interface to docheckBookings below
 * covering period log
 */
function checkperiodBookings($projectTreeId,$startDate,$endDate,$startTime,$endTime,$startTask,$endTask)
{
	global $util;

	$objResponse = new xajaxResponse();

	if(XAJAXDBG) 	$objResponse->assign("msgxajax","innerHTML","checkperiodBookings<br>");


	$oldId = 0;
	$bookedtimes = null;

	$startTimeS = $util->makeTimestamp($startTime);
	$endTimeS = $util->makeTimestamp($endTime);
	$startDateS = $util->makeTimestamp($startDate);
	$endDateS = $util->makeTimestamp($endDate);


	$numDays = (($endDateS - $startDateS) / (24*60*60) + 1);
	if(XAJAXDBG) $objResponse->append("msgxajax","innerHTML","start=".$startDate.' end='.$endDate." = numdays: ".$numDays."<br>");
	if(XAJAXDBG) $objResponse->append("msgxajax","innerHTML","start=".$startDateS.' end='.$endDateS." = numdays: ".$numDays."<br>");
	
	for( $i=0 ; $i<$numDays ; $i++ )
	{
		if(XAJAXDBG) $objResponse->append("msgxajax","innerHTML","numdays=".$numDays.' currdayix='.$i."<br>");

		$curDate = ($startDateS + $i*24*60*60 ); // get the day we are working on now
		if( date('w',$curDate)==0 || date('w',$curDate)==6) // check that it is no weekend day
		continue;

		// save start task for this
		$bookDate = date('d.m.Y',$curDate);
		$bookTime = date('H:i',$startTimeS);
		$taskId = $startTask;

		// bookedtimes is by ref, so we get it back and feed it again in next call
		$ret = docheckBookings($objResponse,$projectTreeId,$taskId,$oldId,$bookDate,$bookTime,'period',$bookedtimes);
		if($ret->htmlretarr['overBooked'] > 0) {
			// we have an overbooking here
			if(XAJAXDBG) $objResponse->append("msgxajax","innerHTML",print_r($ret->htmlretarr['overBooked'],true).' '.print_r($ret->htmlretarr['restAvailable'],true)."<br>");
			break;  // outa here
		}

		// end task for that day
		$bookTime = date('H:i',$endTimeS);
		$taskId = $endTask;
		
		$ret = docheckBookings($objResponse,$projectTreeId,$taskId,$oldId,$bookDate,$bookTime,'period',$bookedtimes);
		
		if($ret->htmlretarr['overBooked'] > 0) {
			// we have an overbooking here
			if(XAJAXDBG) $objResponse->append("msgxajax","innerHTML",print_r($ret->htmlretarr['overBooked'],true).' '.print_r($ret->htmlretarr['restAvailable'],true)."<br>");
			break;  // outa here
		}
	}

	// we either have the ret from an overbooking or the last one without overbooking
	foreach($ret->htmlretarr as $name=>$val) {
		$objResponse->assign($name,"value",$val);
	}
	
	unset($bookedtimes);
	
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
 * & $btimes : in period log we have to collect alle bookings in a loop. So we return the bookings and feed it again
 * 			   in next call
 *
 */
function docheckBookings($objResponse,$projectTreeId,$taskId,$oldId,$bookDate,$bookTime,$calledfrom='today',& $btimes = null)
{
	global $userAuth;
	global $task,$time;


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

	if(XAJAXDBG) $objResponse->append("msgxajax","innerHTML","<br>docheckBookings<br>");

	$projectTree = modules_project_tree::getInstance(true);  // base of that all


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

		// if called from periodlog, we get the btimes from last call	
		if(empty($btimes)) $btimes = AllBookedTimes($projectTreeId);

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

		$dbgupdate = false;
		if (!empty($oldId)) {
			$btimes = updateDuration($dbgupdate,$btimes, $oldData,$objResponse ); // first update the entries around the old time
			$btimes = updateDuration($dbgupdate,$btimes, $oldId,$objResponse ); // update the entries around the updated time
		} else {
			// new stamp just sort it in
			$btimes = updateDuration($dbgupdate,$btimes,$newData,$objResponse);
		}

		if(XAJAXDBG) $objResponse->append("msgxajax","innerHTML",'updated btimes: '.print_r($btimes,true).'<p>');

		$currsum = SumBookedTimes($btimes);

		if(XAJAXDBG) $objResponse->append("msgxajax","innerHTML","Booked sum after update: ".$currsum.'<br>');

		$justbooked = $currsum - $beforesum;
		if(XAJAXDBG) $objResponse->append("msgxajax","innerHTML","Just booked: ".$time->_calcDuration($justbooked,'hour').'<br>');
		
		$rest = $maxDuration - $currsum;
		//$wouldbe = $beforesum + ($beforesum - $currsum);
		if(XAJAXDBG) $objResponse->append("msgxajax","innerHTML","Would have after booking: ".$rest.'('.$time->_calcDuration($rest,'hour').')<br>');

		if($rest < 0) {
			$overbooked = true;
		}

		if($rest < 0) {
			$hrest = '-'.$time->_calcDuration(abs($rest),'hour');
		} else {
			$hrest = $time->_calcDuration($rest,'hour');
		}

		if(XAJAXDBG) $objResponse->append("msgxajax","innerHTML","Rest: ".$rest.'('.$hrest.')<br>');

		if($calledfrom != 'period' && $calledfrom != 'multi' ) unset($btimes);
		
		unset($projectTree);

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
	global $time,$task;

	if($dbg) $objResponse->append("msgxajax","innerHTML","In updateduration: ".print_r($data,true).'<br>');

	if (is_numeric($data)) {
		if($dbg) $objResponse->append("msgxajax","innerHTML",'<br>updatedDuration btimes0: '.print_r($btimes,true).'<p>');
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

	if($dbg) $objResponse->append("msgxajax","innerHTML",'<br>updatedDuration btimes before: '.print_r($btimes,true).'<p>');


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
				if($dbg) $objResponse->append("msgxajax","innerHTML","found no next ".$next['id']."<br>");
				
				// no previous stamp : booking before last start
				$prev = prev($btimes);
				if(!empty($prev)) {
					if($dbg) $objResponse->append("msgxajax","innerHTML","found prev ".$prev['id']."<br>");
				 	$btimes[$d['timestamp']]['durationSec'] = $prev['timestamp']-$d['timestamp'];
				}
				break;
			} else {
				$rows[]=$n;
			}
			$p = prev($btimes);
			if($dbg) $objResponse->append("msgxajax","innerHTML","prev: ".$p['id']."<br>");
			if(!empty($p)) $rows[]=$p;
			$p = prev($btimes);
			if(!empty($p)) $rows[]=$p;
			break;
		}
		$d = next($btimes);
	}


	if($dbg) $objResponse->append("msgxajax","innerHTML","rows: ".print_r($rows,true).'<br>');

	for ($i=1; $i<sizeof($rows); $i++) {
		$ts = $rows[$i-1]['timestamp'];
		if($dbg) $objResponse->append("msgxajax","innerHTML",'row loop : row-1 '.print_r($rows[$i-1],true).'<br>');
		
		// round the time before updating, this depends on the project it belongs to
		$durationSec = $time->roundTime($rows[$i]['timestamp']-$rows[$i-1]['timestamp'],$rows[$i-1]['projectTree_id']);
		if($dbg) $objResponse->append("msgxajax","innerHTML",'Calc duration '.$durationSec.'<br>');		
		if($task->hasDuration($btimes[$ts]['task_id'])) {
			if($dbg) $objResponse->append("msgxajax","innerHTML",'Task has duration. Update with '.$durationSec.'<br>');
			$btimes[$ts]['durationSec'] = $durationSec;
		}
	}

	if($dbg) $objResponse->append("msgxajax","innerHTML",'<br>updatedDuration btimes after: '.print_r($btimes,true).'<p>');

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
$xajax->registerFunction("checkperiodBookings");

// Process any requests.  Because our requestURI is the same as our html page,
// this must be called before any headers or HTML output have been sent
$xajax->processRequest();


header("Content-Type: text/html; charset=".$xajax->sEncoding);
?>