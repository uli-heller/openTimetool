<?php
    //
    //	$Id
    //
    //  Revision 1.11.2.2 2006/08/25 AK
    //  Eliminated a php notice !
    //  
    //  Revision 1.11.2.1  2003/04/10 19:01:26  wk
    //  - get projectTreeDyn instance properly
    //
    //  Revision 1.11  2003/03/04 19:18:32  wk
    //  - dont load unnecessary code when it is not needed
    //
    //  Revision 1.10  2003/02/11 12:00:10  wk
    //  - add proper info to each time-slice
    //
    //  Revision 1.9  2003/02/10 19:27:52  wk
    //  - use projectTreeDyn now
    //
    //  Revision 1.8  2003/01/29 10:39:48  wk
    //  - E_ALL stuff
    //
    //  Revision 1.7  2002/11/30 18:39:11  wk
    //  - show order properly
    //
    //  Revision 1.6  2002/11/13 19:02:21  wk
    //  - nothing serious
    //
    //  Revision 1.5  2002/11/11 18:02:03  wk
    //  - show proper task, since it is sorted differently now
    //
    //  Revision 1.4  2002/10/22 14:44:04  wk
    //  - changed $auth to $userAuth
    //
    //  Revision 1.3  2002/09/23 09:35:12  wk
    //  - added shortcuts
    //
    //  Revision 1.2  2002/07/24 17:10:54  wk
    //  - update headline to use tree
    //
    //  Revision 1.1.1.1  2002/07/22 09:37:37  wk
    //
    //
    //

    $noneProjectTasks = array();
    if( $userAuth->isLoggedIn() )
    {
        // do only require those files when really needed, and that is when the user is logged in
        require_once $config->classPath.'/modules/project/treeDyn.php';
        require_once $config->classPath.'/modules/time/time.php';
        require_once $config->classPath.'/modules/task/task.php';
        $projectTreeDyn =& modules_project_treeDyn::getInstance();
        
        $time->preset();
        $time->setWhere('user_id='.$userAuth->getData('id'));
        $today = array_reverse($time->prepareResult($time->getDay()));

        if (sizeof($today)) {
            foreach ($today as $key=>$aTime) {
                $_title = $dateTime->formatTimeShort($aTime['timestamp']);
                // AK : I hate php notes ;-) ...
                //$_title.= $aTime['duration'] ? " ({$aTime['duration']}) " : ' ';
                if(!isset($aTime['duration'])) $_title.= ' ';
                else $_title.= ' ('.$aTime['duration'].') ';
                $_title.= $projectTreeDyn->getPathAsString($aTime['projectTree_id']);
                $_title.= " - {$aTime['_task_name']}";
                $today[$key]['_title'] = $_title;
            }
        }

        $currentTask = @$today[0];
        $noneProjectTasks = $task->getNoneProjectTasks();
    }

    $isAdmin = $user->isAdmin();

    require_once($config->finalizePage);

?>
