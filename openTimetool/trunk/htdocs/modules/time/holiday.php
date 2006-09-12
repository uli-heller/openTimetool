<?php
    //
    //  $Id
    //  Revision 1.6  2006/08/29 20:34:44  AK
    //  - eliminate php notices
    //
    //  $Log: holiday.php,v $
    //  Revision 1.5  2003/02/11 12:34:44  wk
    //  - use proper JS-file depending on isAdmin
    //
    //  Revision 1.4  2003/02/10 19:14:33  wk
    //  - use projectTreeDyn now
    //
    //  Revision 1.3  2002/10/22 14:26:07  wk
    //  - show only available projects
    //
    //  Revision 1.2  2002/09/11 19:20:41  wk
    //  - show only tasks for end task that dont calcTime
    //
    //  Revision 1.1  2002/08/29 13:25:28  wk
    //  - intial checkin
    //
    //

    require_once $config->classPath.'/modules/time/time.php';
    require_once $config->classPath.'/modules/task/task.php';

    $data = array();
    if( isset($_REQUEST['action_save']) )   // AK : isset to avoid notice
    {
        $data = $_REQUEST['newData'];
        $data['startTime'] = $util->makeTimestamp($data['startTime']);
        $data['endTime'] = $util->makeTimestamp($data['endTime']);
        $data['startDate'] = $util->makeTimestamp($data['startDate']);
        $data['endDate'] = $util->makeTimestamp($data['endDate']);

        if( $data['startTime'] && $data['endTime'] &&
            $data['startDate'] && $data['endDate'] )
        {
            if( $data['startTime'] > $data['endTime'] )
            {
                // switch the start and end time
                $temp = $data['startTime'];
                $data['startTime'] = $data['endTime'];
                $data['endTime'] = $temp;
                // so we have to switch the start and end tasks too!
                $temp = $data['startTask_id'];
                $data['startTask_id'] = $data['endTask_id'];
                $data['endTask_id'] = $temp;

                $applMessage->set('Your start time was later than the end time, they were switched!');
            }
            if( $data['startDate'] > $data['endDate'] )
            {
                $temp = $data['startDate'];
                $data['startDate'] = $data['endDate'];
                $data['endDate'] = $temp;
                $applMessage->set('Your start date was after the end date, they were switched!');
            }

            $save['comment'] = $data['comment'];
            $save['projectTree_id'] = $data['projectTree_id'];
            $save['user_id'] = $userAuth->getData('id');

            // date('w') - day of the week, numeric, i.e. "0" (Sunday) to "6" (Saturday)
            $numDays = (($data['endDate'] - $data['startDate']) / (24*60*60) + 1);
            for( $i=0 ; $i<$numDays ; $i++ )
            {
                $curDate = ($data['startDate'] + $i*24*60*60 ); // get the day we are working on now
                if( date('w',$curDate)==0 || date('w',$curDate)==6) // check that it is no weekend day
                    continue;

                // save start task for this
                $save['timestamp_date'] = date('d.m.Y',$curDate);
                $save['timestamp_time'] = date('H:i',$data['startTime']);
                $save['task_id'] = $data['startTask_id'];
                if( !$time->save($save) )
                    $applError->setOnce('Error saving time for the '.$save['timestamp_date'].'!');
                else
                    $applMessage->setOnce('Time(s) saved.');

                // end task for that day
                $save['timestamp_time'] = date('H:i',$data['endTime']);
                $save['task_id'] = $data['endTask_id'];
                if( !$time->save($save) )
                    $applError->setOnce('Error saving time for the '.$save['timestamp_date'].'!');
                else
                    $applMessage->setOnce('Time(s) saved.');
            }
        }
        else
        {
            $applError->set('Please define a start and end date for your holiday period!');
        }
    }

    if( !sizeof($data) )
    {
        $data['startTime'] = 60*60*8;   // use 9:00 as default
        $data['endTime'] = 60*60*16;    // and 17:00 as end default, those are 8 hours
        $data['startDate'] = time();    // use today as start date
        $data['endDate'] = time();
    }

    $task->setWhere('calcTime<>0'); // start task needs to be a task that calc's time
    $tasks = $task->getAll();

    $task->setWhere('calcTime=0');  // the endtask can only be a task that doesnt get calculated
    $endTasks = $task->getAll();

    $isAdmin = $user->isAdmin();
    $projectTreeJsFile = 'projectTree'.($isAdmin?'Admin':'');

    require_once($config->finalizePage);
?>
