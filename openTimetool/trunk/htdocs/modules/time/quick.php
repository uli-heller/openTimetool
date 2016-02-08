<?php
    //
    //  $Id
    //  Revision 1.11  2006/08/29 19:27:24  AK
    //  - eliminated php notices
    //
    //  $Log: quick.php,v $
    //  Revision 1.10  2003/02/10 19:27:24  wk
    //  - use projectTreeDyn now
    //
    //  Revision 1.9  2002/11/30 13:04:21  wk
    //  - remove old comment
    //
    //  Revision 1.8  2002/11/19 20:01:32  wk
    //  - remove br in error message
    //
    //  Revision 1.7  2002/10/22 14:28:20  wk
    //  - when logging a time again, set the duration to 0
    //
    //  Revision 1.6  2002/09/24 09:42:22  wk
    //  - only replaced # by //
    //
    //  Revision 1.5  2002/08/20 16:28:25  wk
    //  - get only available projects
    //
    //  Revision 1.4  2002/08/14 16:17:55  wk
    //  - use calendar and some smaller changes
    //
    //  Revision 1.3  2002/07/24 17:09:16  wk
    //  - use tree
    //  - show now button and update date automagically
    //
    //  Revision 1.2  2002/07/22 12:03:34  wk
    //  - playing with group-by
    //
    //  Revision 1.1.1.1  2002/07/22 09:37:37  wk
    //
    //
    //


	// as we dont have auto_prepend anymore, we have to include our config here
	require_once("../../../config.php");
	
	require_once $config->classPath.'/pageHandler.php';
    require_once $config->classPath.'/modules/time/time.php';
    require_once $config->classPath.'/modules/task/task.php';
    require_once $config->classPath.'/modules/project/treeDyn.php';

    if( isset($_REQUEST['quickLog']) )  // AK : isset to avoid notice
    {
        if( !$aTime = $time->get($_REQUEST['quickLog']) )
        {
            $applError->set('Sorry, but the Entry you chose was removed meanwhile. Log failed!');
        }
        else
        {
            unset($aTime['id']);
            $aTime['timestamp'] = time();
            $aTime['durationSec'] = 0;
// FIXXME check if the string _ends_ with the string!!!!
            if( strpos($aTime['comment'],'[Quick-Log]') === false )
                $aTime['comment'].= ' [Quick-Log]';
            $_REQUEST['newData'] = $aTime;
            $_REQUEST['action_saveAsNew'] = true;
        }
    }

    $pageHandler->setObject($time);
    $data = $pageHandler->saveHandler( @$_REQUEST['newData'] ); // AK : use @ as if empty a null handler is returned

    $tasks = $task->getAll();

    //$time->setGroup('task_id');
    $time->preset();
    $time->setWhere('user_id='.$userAuth->getData('id'));
//    $time->setGroup('task_id, projectTree_id');
    $time->setOrder('timestamp',true);
    $lastTimes = $time->getAll(0,5);
//print_r($lastTimes);

    $lastTime = @$lastTimes[0];
    $isAdmin = $user->isAdmin();
    $projectTreeJsFile = 'projectTree'.($isAdmin?'Admin':'');

    $layout->setMainLayout('/modules/dialog');
    require_once($config->finalizePage);
?>
