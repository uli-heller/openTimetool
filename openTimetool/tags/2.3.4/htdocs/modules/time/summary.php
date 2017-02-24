<?php
    //
    //  $Log: summary.php,v $
    //  Revision 1.5.2.1  2003/04/10 18:05:36  wk
    //  - use references for getInstance!
    //
    //  Revision 1.5  2003/03/04 19:17:45  wk
    //  - get projectTree by getInstance()
    //
    //  Revision 1.4  2002/08/26 09:09:29  wk
    //  - still searching for the right query
    //
    //  Revision 1.3  2002/08/21 20:22:46  wk
    //  - _hacked_ the prices
    //
    //  Revision 1.2  2002/08/20 16:29:23  wk
    //  - get times sorted by project
    //
    //  Revision 1.1  2002/08/20 09:02:28  wk
    //  - initial commit
    //
    //


	// as we dont have auto_prepend anymore, we have to include our config here
	require_once("../../../config.php");

	require_once($config->classPath.'/modules/time/time.php');
    require_once($config->classPath.'/modules/task/task.php');
#    require_once($config->classPath.'/modules/user/user.php');
    require_once($config->classPath.'/modules/project/tree.php');
/* i was trying this with phpmyadmin, problem is only that i dont get the join properly done
SELECT *
FROM timet
LEFT OUTER JOIN pricep ON ( p.projectTree_id = t.projectTree_id ) OR (
p.projectTree_id = 0
)
WHERE (
p.validFrom < t.timestamp AND (
p.validUntil = 0 OR p.validUntil > t.timestamp
)
) AND t.task_id = p.task_id
*/

/*    $times = $time->execute('SELECT projectTree_id,projectTree.name as project,task.name as task,SUM(durationSec) as total '.
                            'FROM time,task,projectTree '.
                            'WHERE projectTree_id=projectTree.id AND task_id=task.id AND task.calcTime=1 '.
                            'GROUP BY projectTree_id,task_id ORDER BY projectTree_id,task');
*/
    $times = $time->execute('SELECT time.projectTree_id,projectTree.name as project,task.name as task,'.
                            'SUM(durationSec) as totalTime,'.
                            'SUM(durationSec)/(60*60)*price.internal as totalInternal,'.
                            'SUM(durationSec)/(60*60)*price.external as totalExternal '.
                            'FROM time,task,projectTree,price '.
                            'WHERE time.projectTree_id=projectTree.id AND time.task_id=task.id AND task.calcTime=1 '.
                            'AND price.task_id=task.id '.
                            'GROUP BY time.projectTree_id,time.task_id ORDER BY time.projectTree_id,task');
    // collect the keys of the times and match them to the project they belong to
    // so we can access the times via the project id in the tpl
    $timesKeys = array();
    foreach( $times as $key=>$aTime )
        $timesKeys[$aTime['projectTree_id']][] = $key;


    $projectTree = modules_project_tree::getInstance(true);
    $projects = $projectTree->getAllVisible();

    require_once($config->finalizePage);
?>
