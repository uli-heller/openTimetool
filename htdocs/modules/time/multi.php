<?php
    //
    //  $Id
    //
    //  Revision 1.4.2.2 2006/25/08
    //  Eliminated some php notices : isset instead of sizeof
    //
    //  Revision 1.4.2.1  2003/04/10 18:05:36  wk
    //  - use references for getInstance!
    //
    //  Revision 1.4  2003/03/04 19:17:25  wk
    //  - get projectTree by getInstance()
    //  - CS
    //
    //  Revision 1.3  2002/11/13 19:01:46  wk
    //  - some admin handling
    //
    //  Revision 1.2  2002/10/22 14:27:04  wk
    //  - save the times now
    //
    //  Revision 1.1  2002/10/21 18:26:56  wk
    //  - initial commit
    //
    //


    require_once($config->classPath.'/pageHandler.php');
    require_once($config->classPath.'/modules/time/time.php');
    require_once($config->classPath.'/modules/task/task.php');
    require_once($config->classPath.'/modules/project/tree.php');
    $projectTree =& modules_project_tree::getInstance(true);

	//AK : use isset instead of size ; php5 ?
    if (isset($_REQUEST['newData'])) {
        $saved = 0;
        foreach ($_REQUEST['newData'] as $aNew) {
            if (!$aNew['timestamp_date'] || !$aNew['timestamp_time'] || !$aNew['task_id']) {
                continue;
            }
            $_data = $aNew;
            $_data['user_id'] = $_REQUEST['user_id'];
            if ($time->save($_data)) {
                $saved++;
            } else {
                $data[] = $_data;
            }
        }
        if ($saved) {
            $applMessage->set("$saved datasets successfully saved.");
        }
        if (!isset($data)) {   // AK : instead of sizeof -> php5 throws a notice
            $applError->set('Please correct the data of the shown entries!');
        }
    }

    $isAdmin = $user->isAdmin();
    if ($isAdmin) {
        $users = $user->getAll();
    }
    
    $tasks = $task->getAll();
    $allFolders = $projectTree->getAllAvailable();
    // AK : I hate those php notices !!
    //$userId = $_REQUEST['user_id'] ? $_REQUEST['user_id'] : $userAuth->getData('id');
    (isset($_REQUEST['user_id']))?$userId = $_REQUEST['user_id'] : $userId = $userAuth->getData('id');

    require_once($config->finalizePage);
?>
