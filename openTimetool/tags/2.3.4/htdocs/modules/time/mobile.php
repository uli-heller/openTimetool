<?php
    //
    //  $Id
    //
	// 	mobile : copy of quick for access by mobile phones and pdas
	// 	
	// 	Well seems to be cool : by just adding php&tpl without any other entries in any 
	// 	arrays or classes, I'll get that page without navigation and stuff I don't need
	// 	Comes because MainLayout is "modules/dialog" set in mobile.php. Same as quick_log
	// 	we copied from
	// 	Well in init.php I defined the page-header. looks better ...
	// 	quick_log is defined as popup-window in modules/navigation.php by the way. As
	// 	we don't have mobile there, it doesn't appear in menu
    //
    //
    //


	// as we dont have auto_prepend anymore, we have to include our config here
	require_once("../../../config.php");

    require_once $config->classPath.'/pageHandler.php';
    require_once $config->classPath.'/modules/time/time.php';
    require_once $config->classPath.'/modules/task/task.php';
    //require_once $config->classPath.'/modules/project/treeDyn.php';
    
    /**
     * We don't use dynTree as this needs Jscript
     */
    require_once('vp/Application/HTML/Tree.php');
    require_once($config->classPath.'/modules/project/tree.php');
    $projectTree = modules_project_tree::getInstance();    
    $allFolders = $projectTree->getAllAvailable();    

    $pageHandler->setObject($time);
    $data = $pageHandler->saveHandler( @$_REQUEST['newData'] ); // AK : use @ as if empty a null handler is returned

	// we set the time/date to the current datetime when page is redrwan
	$data['timestamp'] = mktime(date('H'),date('i'),date('s'),date('m'),date('d'),date('Y'));

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
    $projectTreeJsFile = 'projectTree'.($isAdmin?'Admin':'');  // we use the static tree version; see above

    $layout->setMainLayout('/modules/dialog');   // to get the page with all that stuff around
    require_once($config->finalizePage);
?>
