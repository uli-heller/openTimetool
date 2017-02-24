<?php
   /**
    * 
    *  $Id
    * 
    *   Revision 1.24  2006/08/29 19:45:09  AK
    *   Eliminating php notices
    * 
    * *********** Switch to SVN ************
    *   $Log: index.php,v $
    *   Revision 1.23  2003/02/17 19:17:09  wk
    *   - make selecting a project work again
    * 
    *   Revision 1.22  2003/02/10 19:27:17  wk
    *   - use projectTreeDyn now
    * 
    *   Revision 1.21  2003/02/10 16:14:14  wk
    *   - prepare result before using (so to speek use calc the duration)
    * 
    *   Revision 1.20  2003/01/30 16:13:14  wk
    *   - compliant to CS
    *   - use getFiltered
    * 
    *   Revision 1.19  2002/12/05 14:19:50  wk
    *   - correct window size
    * 
    *   Revision 1.18  2002/11/29 16:56:19  wk
    *   - fix inconsequent programming bugs
    *   - remove stuff that the I18N handles
    * 
    *   Revision 1.17  2002/11/26 15:59:49  wk
    *   - added extended filter
    * 
    *   Revision 1.16  2002/11/25 10:49:21  wk
    *   - set window height according to the number of elements
    * 
    *   Revision 1.15  2002/11/19 20:01:19  wk
    *   - make day names translateable ... FIXXME do better
    * 
    *   Revision 1.14  2002/11/13 19:01:33  wk
    *   - some admin handling
    * 
    *   Revision 1.13  2002/11/11 17:58:30  wk
    *   - show times in chronological order during a day
    * 
    *   Revision 1.12  2002/10/28 11:20:27  wk
    *   - show only projects the user is allowed to see
    * 
    *   Revision 1.11  2002/10/24 14:13:20  wk
    *   - use the saveHandler the new way. correctly now!
    * 
    *   Revision 1.10  2002/10/22 14:26:42  wk
    *   - changed $auth to $userAuth
    * 
    *   Revision 1.9  2002/09/24 09:47:20  wk
    *   - preset before building the query
    * 
    *   Revision 1.8  2002/08/30 18:45:06  wk
    *   - bugfix, see code
    * 
    *   Revision 1.7  2002/08/20 16:27:26  wk
    *   - show edit data again if saving failed
    * 
    *   Revision 1.6  2002/08/14 16:17:22  wk
    *   - added comment-search
    *   - show only the current users entries if no user chosen
    * 
    *   Revision 1.5  2002/07/30 20:23:51  wk
    *   - allow multiselects
    * 
    *   Revision 1.4  2002/07/25 11:56:31  wk
    *   - save graphMode in session
    * 
    *   Revision 1.3  2002/07/25 10:09:45  wk
    *   - if no show-data given use current user
    * 
    *   Revision 1.2  2002/07/24 17:08:20  wk
    *   - merged former view file in here
    * 
    *   Revision 1.1.1.1  2002/07/22 09:37:37  wk
    * 
    * 
    */

    require_once $config->classPath.'/pageHandler.php';
    require_once $config->classPath.'/modules/time/time.php';
    require_once $config->classPath.'/modules/task/task.php';
    require_once $config->classPath.'/modules/user/user.php';
    require_once $config->classPath.'/modules/project/treeDyn.php';
    require_once $config->classPath.'/modules/project/member.php';


    if (isset($_POST['action_extendedFilter'])) {	// AK : isset to avoid php notice
    	// AK isset to avoid notices
    	//if(isset($session->temp->time)) 
        	$extendedFilter = @$session->temp->time->extendedFilter = !@$session->temp->time->extendedFilter;
        //else
        //	$extendedFilter = null;
        if (!$extendedFilter) {
            if(isset($_REQUEST['show']['projectTree_ids'])) 
            	unset($_REQUEST['show']['projectTree_ids']);
            if(isset($session->temp->time_index))
            	unset($session->temp->time_index);
        }
    }   
    // AK isset to avoid php notice
	if(isset($session->temp->time))
    	$extendedFilter = $session->temp->time->extendedFilter;
    else
    	$extendedFilter = null;
    $show = &$session->temp->time_index;
        
//echo "0 data : ";print_r($data);echo"<p>"; 
//echo "0 NewData : ";print_r($_REQUEST['newData']);echo"<p>"; 
    // those two lines handle the edit functionality
    $pageHandler->setObject($time);
    if (!$pageHandler->save( @$_REQUEST['newData'])) {  // AK : if newData not in REQUEST, save returns an empty object
//echo "save false";
        $data = $pageHandler->getData();
        // convert the time and date, so the macro can show it properly ... do this better somehow
        // AK : use isset to avoid php notices
        if (isset($data['timestamp_date']) && isset($data['timestamp_time']) && !isset($data['timestamp'])) {
            $_date = explode('.',$data['timestamp_date']);
            $_time = explode(':',$data['timestamp_time']);
            $data['timestamp'] = mktime($_time[0],$_time[1],0,$_date[1],$_date[0],$_date[2]);
        }
    }
//echo "1 : ";print_r($data);echo"<p>"; 
    // this handles the remove-functionality
    if (isset($_REQUEST['removeId'])) {   // AK : use isset to avoid php notices
        $time->remove($_REQUEST['removeId']);
    }

    // this takes care of saving the show-parameters in the session
    // and retreiving them from there
    if (isset($_REQUEST['show'])) {   // AK : use isset to avoid php notices
//print "<br>show<br>";print_r($_REQUEST['show']);    
        $show = $_REQUEST['show'];
    }
    if (!$show) {
        $show['user_id'] = $userAuth->getData('id');
    }

//echo "2 : ";print_r($data);echo"<p>"; 
//print_r($show);echo"<p>";
    if (isset($_REQUEST['action_showToday'])) {  // AK : use isset to avoid php notices
        unset($show['humanDateFrom']);
        unset($show['humanDateUntil']);
    }
    // convert the dates and set the where-clauses
    if( isset($show['humanDateFrom']) )    // AK : use isset to avoid php notices
        $dateFrom = explode('.',$show['humanDateFrom']);
    if( isset($show['humanDateUntil']) )   // AK : use isset to avoid php notices
        $dateUntil = explode('.',$show['humanDateUntil']);
    if( isset($dateFrom) )
        $show['dateFrom'] = mktime(0,0,0,$dateFrom[1],$dateFrom[0],$dateFrom[2]?$dateFrom[2]:date('Y'));
    else
        $show['dateFrom'] = time();

    if( isset($dateUntil) )
        $show['dateUntil'] = mktime(0,0,0,$dateUntil[1],$dateUntil[0],$dateUntil[2]?$dateUntil[2]:date('Y'));
    else
        $show['dateUntil'] = time();

    // handle the buttons
    if (isset($_REQUEST['action_showDayPlus1'])) {  // AK : use isset to avoid php notices
        $show['dateUntil'] = $show['dateUntil'] + 60*60*24;
        $show['dateFrom'] = $show['dateFrom'] + 60*60*24;
    }
    if (isset($_REQUEST['action_showWeekPlus1'])) {  // AK : use isset to avoid php notices
        $show['dateUntil'] = $show['dateUntil'] + 60*60*24*7;
        $show['dateFrom'] = $show['dateFrom'] + 60*60*24*7;
    }
    if (isset($_REQUEST['action_showDayMinus1'])) {  // AK : use isset to avoid php notices
        $show['dateUntil'] = $show['dateUntil'] - 60*60*24;
        $show['dateFrom'] = $show['dateFrom'] - 60*60*24;
    }
    if (isset($_REQUEST['action_showWeekMinus1'])) {  // AK : use isset to avoid php notices
        $show['dateUntil'] = $show['dateUntil'] - 60*60*24*7;
        $show['dateFrom'] = $show['dateFrom'] - 60*60*24*7;
    }
    // convert the dateUntil and dateFrom back to humanDate... so this is saved in the session too
    $show['humanDateFrom'] = date('d.m.Y',$show['dateFrom']);
    $show['humanDateUntil'] = date('d.m.Y',$show['dateUntil']);

    // set the authenticated user's id if none was chosen in the frontend
    $isManager = $projectMember->isManager();
    if (!isset($show['user_ids']) || !$isManager) {  // AK isset to avoid notice
        $show['user_ids'] = array();    // empty the array and show only this users data!!!
        $show['user_ids'][0] = $userAuth->getData('id');
    }
               
    $curUserId = $userAuth->getData('id');
    $isAdmin = $user->isAdmin();


    // getFiltered uses only some of the data in the show-array, but exactly those we need :-)
    // it takes care of stuff like user_ids, projectTree_ids, etc.
    $show['timestamp_start'] = $show['dateFrom'];
    $show['timestamp_end'] = $show['dateUntil'];

    if ($_times = $time->prepareResult($time->getFiltered($show))) {
        $_lastDate = 0;
        $times = array();
        foreach ($_times as $aTime) {
            $_date = date('dmY',$aTime['timestamp']);
            // AK : use !empty instead of sizeof($aDayTimes)
            if ($_date != $_lastDate && !empty($aDayTimes)) {
                $times = array_merge($times,array_reverse($aDayTimes));
                $aDayTimes = array();
            }
            // set an additional field, which we can check in the template to know if the current user
            // can edit this entry
            $aTime['_canEdit'] = $isAdmin || $curUserId == $aTime['_user_id'];
            $aDayTimes[] = $aTime;
            $_lastDate = $_date;
        }
        $times = array_merge($times,array_reverse($aDayTimes)); // add the last day too :-)
    }
    
   /**
    * We'll get here when using the export button. "Nach Datum" und "Export" time/index.php is called
    * where you can find this button
    *  
    * lets get the number of exported files and show at most 10 of them
    * and adapt the size of the window, i.e. if only 3 are visible the window will
    * be so big that 3 items will be properly shown without scroll :-) nice little feature
    */
    if (isset($_REQUEST['action_export'])) {  // AK : use isset to avoid php notices
        require_once $config->classPath.'/modules/export/export.php';
        $expCount = $export->getCount();
        $exportWinHeight = 410 + (min(10,$expCount) * 30);
    }
    else 
    	$exportWinHeight = 410;  // AK : to avoid php notices

    $tasks = $task->getAll();
    $users = $user->getAllAvail();
    $projectTreeJsFile = 'projectTree'.($isAdmin?'Admin':'');

    require_once($config->finalizePage);
?>
