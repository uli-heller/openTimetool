<?php
    /**
     * $Id
     * 
     * This script runs when called by fopen in OOoExport.php.
     * It modifies the content.xml of an uncompressed OpenDocument-file
     * See OOoTemplate for some details
     * Language compilation is done by this as well
     * 
     * Revision 1.8 2009/09/28  Enhancement triggered by HiSec.at
     * - We provide now a 3rd array with all projects 
     * - Within this array we have the "old" user array
     * - All Levels are now with sum figures 
     * 
     * Revision 1.7 2009/09/17	Bugfix by hisec AT (HS)
     * 
     * **** Switch to SVN *******
     *  $Log: processOOoFile.php,v $
     *  Revision 1.6  2003/02/17 19:16:36  wk
     *  - use the session if we have it :-)
     *  - make new tpl instance
     *
     *  Revision 1.5  2003/01/13 18:12:20  wk
     *  - sort the dates properly
     *  - use Xipe,
     *  - set additional tpl-vars
     *
     *  Revision 1.4  2002/12/02 20:40:31  wk
     *  - encode dates and show full dates
     *
     *  Revision 1.3  2002/12/02 20:22:00  wk
     *  - encode data properly and show data properly, bugfix
     *
     *  Revision 1.2  2002/11/12 18:00:00  wk
     *  - prepare varibales properly
     *
     *  Revision 1.1  2002/11/11 17:57:15  wk
     *  - initial commit
     *
    */

    function OOencode( $string )
    {
        return utf8_encode(str_replace("\n", "<text:line-break />", htmlspecialchars($string)));   // HS
    }

//FIXXXXXME go thru all fields properly and OOencode them, put OOencode in PEAR or in $util, or in HTML/Template/Xipe

    // get the data to fill the template
    require_once($config->classPath.'/modules/project/tree.php');
    require_once($config->classPath.'/modules/time/time.php');

    // so we dont get no php-errors in the template, which might make it invalid
    // i.e if a foreach has no values to go thru
    ini_set('display_errors',0);

    $options = array(   'templateDir'   => $config->applRoot,
                        'compileDir'    => 'tmp',   // use the compile dir 'tmp' under the tempalte dir
                        'verbose'   => false,        // this is default too
                        'logLevel'  => 0,           // dont write log files
                        'filterLevel'   => 9,      // apply all the most common filters
                        'locale'        =>  'en',    //
                        'autoBraces'    => false    // we use foreach endforeach here
                        );
    $tpl = new HTML_Template_Xipe($options);
    $show = $session->temp->time_index;



// copy from time/index.php!!!!
    // set the authenticated user's id if none was chosen in the frontend
    $isManager = $projectMember->isManager();
    if( !$show['user_ids'] || !$isManager )
    {
        $show['user_ids'] = array();    // empty the array and show only this users data!!!
        $show['user_ids'][0] = $userAuth->getData('id');
    }

    $time->preset();
    if( $show['comment'] )
        $time->addWhereSearch( TABLE_TIME.'.comment' , $show['comment'] );
    if( $show['projectTree_ids'] )
        $time->addWhere(TABLE_PROJECTTREE.'.id IN('.implode(',',$show['projectTree_ids']).')');
    if( sizeof($show['task_ids']) )
        $time->addWhere(TABLE_TASK.'.id IN('.implode(',',$show['task_ids']).')');
    if( $show['user_ids'] )
        $time->addWhere(TABLE_USER.'.id IN('.implode(',',$show['user_ids']).')');

    $times = $time->getDay($show['dateFrom'],$show['dateUntil']);
// copy end


    $timespanFrom = OOencode($dateTime->formatDate($show['dateFrom']));
    $timespanUntil = OOencode($dateTime->formatDate($show['dateUntil']));



    // put all the users in the first level of the array
    $users = array();
    foreach( $times as $aTime )
    {               
//        if( $aTime['_task_calcTime'] )  // only if there is a time for this task, add it to the sum ('Gehen' has no time)
//        {
        $curUserId = $aTime['user_id'];
        if( !$users[$curUserId] )               // if this user has not been found yet, set his username
        {
            $users[$curUserId]['name'] = OOencode($aTime['_user_name']);
            $users[$curUserId]['surname'] = OOencode($aTime['_user_surname']);
        }                              

        if( $aTime['_task_calcTime'] )  // only if there is a time for this task, put it in the array ('Gehen' has no time)
            $aTime['duration'] = $time->_calcDuration( $aTime['durationSec'] , 'decimal' );

        $aTime['task'] = OOencode($aTime['_task_name']);
        $users[$curUserId]['days'][] = $aTime;
        if( $aTime['_task_calcTime'] )  // only if there is a time for this task, add it to the sum ('Gehen' has no time) by HS
            $sum += $aTime['durationSec'];
//        }
    }
    // this is the overall sum !!
    $sum = $time->_calcDuration( $sum , 'decimal' );

    foreach( $users as $key=>$aUser )
    {
        $newDays = array(); // so the last user doesnt get all the times
        foreach( array_reverse($aUser['days']) as $aTime )
        {
            $aTime['comment'] = OOencode($aTime['comment']);

            $dayIndex = date('dmY',$aTime['timestamp']);
            $newDays[$dayIndex]['times'][] = $aTime;

            $newDays[$dayIndex]['date'] = OOencode($dateTime->formatDate($aTime['timestamp']));
            $newDays[$dayIndex]['dateShort'] = OOencode($dateTime->formatDateShort($aTime['timestamp']));
            $newDays[$dayIndex]['dateLong'] = OOencode($dateTime->formatDateLong($aTime['timestamp']));
            $newDays[$dayIndex]['dateFull'] = OOencode($dateTime->formatDateFull($aTime['timestamp']));
        }
        $users[$key]['days'] = $newDays;
    }
    
    /**
     * now build the project view array for ootemplating (in principle just one level around the stuff above)
     * with sums for each project and each user 
     */
    $projects = array();
    // you can switch on debugging here. Output is written to a file on server
    $dbg = false;
   	if($dbg) {
		$f = fopen($config->applRoot.'/tmp/ooexport.txt','wb');
	}  
	// build the project array and calc the project sums in sec
    foreach( $times as $aTime )
    {               
        $curProjectId = $aTime['_projectTree_id'];
        if( !$projects[$curProjectId] )               // if this project has not been found yet, set its name
        {
            $projects[$curProjectId]['projectname'] = OOencode($aTime['_projectTree_name']);
           	$projects[$curProjectId]['sum'] = 0;
        }   
        if( $aTime['_task_calcTime'] ) {  // only if there is a time for this task, add it to the sum ('Gehen' has no time) by HS           
            $projects[$curProjectId]['sum'] += $aTime['durationSec'];
        }                                   
    }
    // now we need to have the same stuff as for user array above ... per project
    foreach( $projects as $key=>$project ) {
    	$curProjectId = $key;
	    // put all the users in the first level of the array
    	$pusers = array();
	    foreach( $times as $aTime )
    	{               
    		if($dbg) {
				//fwrite($f,"cpid=".$curProjectId.' == '.$aTime['_projectTree_id']);
    		}	
    		if ($curProjectId == $aTime['_projectTree_id']) {
		        $curUserId = $aTime['user_id'];
    		    if( !$pusers[$curUserId] )               // if this user has not been found yet, set his username
        		{
            		$pusers[$curUserId]['name'] = OOencode($aTime['_user_name']);
	            	$pusers[$curUserId]['surname'] = OOencode($aTime['_user_surname']);
	            	$pusers[$curUserId]['sum'] = 0;
	    	    }                              

    	    	if( $aTime['_task_calcTime'] )  // only if there is a time for this task, put it in the array ('Gehen' has no time)
        	    	$aTime['duration'] = $time->_calcDuration( $aTime['durationSec'] , 'decimal' );

		        $aTime['task'] = OOencode($aTime['_task_name']);
    		    $pusers[$curUserId]['days'][] = $aTime;
        		if( $aTime['_task_calcTime'] ) { // only if there is a time for this task, add it to the sum ('Gehen' has no time) by HS
            		$pusers[$curUserId]['sum'] += $aTime['durationSec'];
        		}
	    	}
    	}

    	foreach( $pusers as $key=>$aUser )
    	{
	        $newDays = array(); // so the last user doesnt get all the times
    	    foreach( array_reverse($aUser['days']) as $aTime )
        	{
	            $aTime['comment'] = OOencode($aTime['comment']);
			
    	        $dayIndex = date('dmY',$aTime['timestamp']);
    	        
    	        // calc day sum
				if( $aTime['_task_calcTime'] ) { // only if there is a time for this task, add it to the sum ('Gehen' has no time) by HS
           			$newDays[$dayIndex]['sum'] += $aTime['durationSec'];
				}
    	        
        	    $newDays[$dayIndex]['times'][] = $aTime;

	            $newDays[$dayIndex]['date'] = OOencode($dateTime->formatDate($aTime['timestamp']));
    	        $newDays[$dayIndex]['dateShort'] = OOencode($dateTime->formatDateShort($aTime['timestamp']));
        	    $newDays[$dayIndex]['dateLong'] = OOencode($dateTime->formatDateLong($aTime['timestamp']));
            	$newDays[$dayIndex]['dateFull'] = OOencode($dateTime->formatDateFull($aTime['timestamp']));
            	
	        }
			if($dbg) {
				//fwrite($f,"newDays:".print_r($newDays,true));
			}	        
	        // transform the sum from sec to dec hours 4 all newDays
    	    foreach($newDays as $dkey=>$newTimeswithSum) {
    	    	$newDays[$dkey]['sum'] = $time->_calcDuration( $newDays[$dkey]['sum'] , 'decimal' );
    	    }
	        	
    	    $pusers[$key]['days'] = $newDays;
    	    
    	    
    	    // this is the sum of user in decimal notation
    		$pusers[$key]['sum'] = $time->_calcDuration( $pusers[$key]['sum'] , 'decimal' );
    	    
    	}    
    	$projects[$curProjectId]['users']	= $pusers;
		if($dbg) {
			fwrite($f,"Pusers:".print_r($pusers,true));
		}
		
		// this is the project sum in decimal notation
		$projects[$curProjectId]['sum'] = $time->_calcDuration( $projects[$curProjectId]['sum'] , 'decimal' );
		
    }
        
	if($dbg) {
		fwrite($f,"Times:".print_r($times,true));
		fwrite($f,"Users:".print_r($users,true));
		fwrite($f,"Projects:".print_r($projects,true));
		fclose($f);
	}

/*
        if( !$users[$aTime['user_id']] )
        {
            $users[$aTime['user_id']]['username'] = $aTime['_user_name'].' '.$aTime['_user_surname'];
            $users[$aTime['user_id']]['email'] = $aTime['email'];
        }
        $users[$aTime['user_id']]['days'][''] = $aTime;
*/


    $tpl->compile($session->temp->OOoExport->xmlFile);
//print $tpl->getCompiledTemplate();
    include $tpl->getCompiledTemplate();

	// don't activate. Will be written into content.xml ...
    //require_once($config->finalizePage);

?>