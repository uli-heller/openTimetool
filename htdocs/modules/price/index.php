<?php
    //
    //  $Log: index.php,v $
    //  Revision 1.7.2.1  2003/04/10 18:05:36  wk
    //  - use references for getInstance!
    //
    //  Revision 1.7  2003/03/04 19:14:12  wk
    //  - get projectTree by getInstance()
    //
    //  Revision 1.6  2002/10/24 14:11:39  wk
    //  - use the saveHandler the new way. correctly now!
    //
    //  Revision 1.5  2002/09/02 11:29:15  wk
    //  - added previous next logic
    //
    //  Revision 1.4  2002/08/30 18:43:33  wk
    //  - price uses the proper join, so we can simplify this here
    //
    //  Revision 1.3  2002/08/26 09:08:11  wk
    //  - sort prices by name
    //
    //  Revision 1.2  2002/08/21 20:21:33  wk
    //  - added real functionality
    //
    //  Revision 1.1  2002/08/20 09:02:13  wk
    //  - initial commit
    //
    //


	// as we dont have auto_prepend anymore, we have to include our config here
	require_once("../../../config.php");

	require_once($config->classPath.'/modules/user/user.php');
    require_once($config->classPath.'/modules/project/tree.php');
    require_once($config->classPath.'/modules/task/task.php');
    require_once($config->classPath.'/modules/price/price.php');
    require_once($config->classPath.'/pageHandler.php');
    require_once('vp/Application/HTML/NextPrev.php');

    if( $_REQUEST['removeId'] )
        $price->remove( $_REQUEST['removeId'] );

    $pageHandler->setObject($price);
    if( !$pageHandler->save( $_REQUEST['newData'] ) )
    {
        $data = $pageHandler->getData();
    }

    // get the data for the add/edit fields
    $users = $user->getAll();
    $projectTree = modules_project_tree::getInstance(true);
    $projects = $projectTree->getNode();
    $tasks = $task->getAll();

    // get all the prices for the overview
    $price->setOrder('name');   // sort them nicely by name
    $nextPrev = new vp_Application_HTML_NextPrev($price);
    $prices = $nextPrev->getData();

    require_once($config->finalizePage);

?>
