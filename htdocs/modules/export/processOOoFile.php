<?php
    /**
     * $Id
     * 
     * This script runs when called by fopen in OOoExport.php.
     * It modifies the content.xml of an uncompressed OpenDocument-file
     * See OOoTemplate for some details
     * Language compilation is done by this as well
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
        return utf8_encode(htmlspecialchars($string));
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
        $sum += $aTime['durationSec'];
    }
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
//echo "Times:".print_r($times,true).'<p>';
//echo "Users:".print_r($users,true).'<p>';

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
