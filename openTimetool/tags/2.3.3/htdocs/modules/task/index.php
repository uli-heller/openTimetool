<?php
    //
    //  $Log: index.php,v $
    //  Revision 1.5  2003/02/13 16:18:56  wk
    //  - prevent no admins on this page
    //
    //  Revision 1.4  2002/10/24 14:13:00  wk
    //  - use the saveHandler the new way. correctly now!
    //
    //  Revision 1.3  2002/09/02 11:29:15  wk
    //  - added previous next logic
    //
    //  Revision 1.2  2002/08/20 09:04:53  wk
    //  - added remove
    //
    //  Revision 1.1.1.1  2002/07/22 09:37:37  wk
    //
    //
    //

    require_once($config->classPath.'/pageHandler.php');
    require_once($config->classPath.'/modules/task/task.php');
    require_once('vp/Application/HTML/NextPrev.php');

    if (!$user->isAdmin()) {
        require_once 'HTTP/Header.php';
        HTTP_Header::redirect($config->home);
    }

    if (isset($_REQUEST['removeId'])) {		// AK : isset added
        $task->remove( $_REQUEST['removeId'] );
    }

    $pageHandler->setObject($task);
    if (!$pageHandler->save( @$_REQUEST['newData'])) {  // AK : "!isset($_REQUEST['newData']) &&"  added
        $data = $pageHandler->getData();
    }

    $nextPrev = new vp_Application_HTML_NextPrev($task);
    $nextPrev->setLanguage( $lang );
    $tasks = $nextPrev->getData();

    require_once($config->finalizePage);
?>
