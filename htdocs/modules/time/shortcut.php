<?php
/**
 * 
 * $Id$
 * 
 */

require_once '../../../config.php';

require_once $config->classPath . '/modules/time/time.php';
require_once $config->classPath . '/modules/project/tree.php';
require_once $config->classPath . '/modules/project/member.php';

$error = false;

if (isset($_REQUEST['shortcutTaskId']) && is_array($_REQUEST['shortcutTaskId'])) {
    // actually there can only be one at a time, but anyway ... we foreach through it
    // since we have the data in the key of the array ... and because i am lazy
    foreach ($_REQUEST['shortcutTaskId'] as $taskId => $x) {
        // get the last used projectTree_id, to be sure a project is set!!!
//FIXXXXME do we really need a projectId, can we not set it to 0????? since this event doenst need a project anyway!!!
        $time->reset();
        $time->setSelect('projectTree_id');
        $time->setWhere('user_id=' . $userAuth->getData('id'));
        $time->setOrder('timestamp', true);
        $lastTime = $time->getAll(0, 1);
        $projectId = $lastTime[0]['projectTree_id'];
        // check if the project is available, if not use root-id
//FIXXME this is way too complicated, make a method like: $project->getAvailable(0,1) or something like this
        $projectTree = modules_project_tree::getInstance(true);
        if (!$projectId || !$projectTree->isAvailable($projectId, time())) {
            if (sizeof($availableProjects = $projectTree->getAllAvailable())) {
                foreach ($availableProjects as $aProject) {
                    if ($projectMember->isMember( $aProject['id'])) {
                        $projectId = $aProject['id'];
                        break;
                    }
                }
            }             

            if (!$projectId ) {
                $applError->set('You are not a team member of any project, please contact your admin!');
                $error = true;
            }
        }

        if ($projectId) {
            $id = $time->save(array(
                'task_id'        => $taskId,
                'projectTree_id' => $projectId,
                'user_id'        => $userAuth->getData('id'),
                'timestamp'      => time(),
            ));
            $time->reset();
            if ($id) {
                $logged = $time->get($id);
            }
        }
    }
}

if ($error) {
    require_once $config->finalizePage;
} else {
    header('Location: today.php');
}
