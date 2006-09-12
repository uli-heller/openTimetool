<?php
    //
    //  $Log: project.php,v $
    //  Revision 1.3.2.4  2003/04/10 18:05:36  wk
    //  - use references for getInstance!
    //
    //  Revision 1.3.2.3  2003/03/31 18:50:56  wk
    //  - if needsProject=1 then we calc the time for the project
    //
    //  Revision 1.3.2.2  2003/03/28 14:19:50  wk
    //  - rounding was screwed
    //
    //  Revision 1.3.2.1  2003/03/27 16:04:41  wk
    //  - show expired projects using the disabled color
    //
    //  Revision 1.3  2003/02/18 20:30:17  wk
    //  - calc only times that need to be calculated!
    //
    //  Revision 1.2  2003/02/18 20:17:50  wk
    //  - show warning if no data available
    //
    //  Revision 1.1  2003/02/18 20:13:47  wk
    //  - project overview
    //
    //

    require_once $config->classPath.'/modules/time/time.php';
    require_once $config->classPath.'/modules/project/treeDyn.php';
    require_once $config->classPath.'/modules/project/member.php';
    require_once $config->classPath.'/modules/project/tree.php';

    $milestones = array(
                        array('percentage'=>0,'color'=>'green')
                        ,array('percentage'=>80,'color'=>'orange')
                        ,array('percentage'=>100,'color'=>'red')
                    );
    if (!$projectMember->isManager()) {
        $applError->set('You are not a project manager of any project!');
    } else {
        $isAdmin = $user->isAdmin();
        
        $time->preset();
        $time->setSelect('SUM(durationSec) AS durationSumSec,projectTree_id,maxDuration');
        if (!$isAdmin) {
            $myProjects = $projectMember->getManagerProjects();
            foreach ($myProjects as $aProject) {
                $projectIds[] = $aProject['projectTree_id'];
            }
            $time->setWhere('projectTree_id IN ('.implode(',',$projectIds).')');
        }
        $time->addWhere(TABLE_TASK.'.calcTime=1 AND '.TABLE_TASK.'.needsProject=1');
        $time->setOrder(TABLE_PROJECTTREE.'.l');
        $time->setGroup('projectTree_id');
        if ($times = $time->getAll()) {

            $projectTree =& modules_project_tree::getInstance();
            $largestDuration = 0;
            foreach ($times as $key=>$aTime) {
                $times[$key]['_durationSum'] = $time->_calcDuration($aTime['durationSumSec'],'decimal');
                $times[$key]['_name'] = $projectTreeDyn->getPathAsString($aTime['projectTree_id']);
                if ($times[$key]['maxDuration']) {  // we dont want to divide by zero
                    $times[$key]['_percent'] = round(($times[$key]['_durationSum']/$times[$key]['maxDuration'])*100,2);
                }
                $times[$key]['_isProjectAvail'] = $projectTree->isAvailable($times[$key]['projectTree_id'],time());
            }

            foreach ($times as $key=>$aTime) {
				// AK : avoid php notices
				if(isset($times[$key]['_percent']))             	
                	$times[$key]['_width'] = $times[$key]['_percent'];
                else 
                	$times[$key]['_width'] = 0;
                foreach ($milestones as $aMilestone) {
                    if (isset($times[$key]['_percent']) && ($times[$key]['_percent']<$aMilestone['percentage'])) {
                        break;
                    }
                    $lastColor = $aMilestone['color'];
                }
                $times[$key]['_color'] = $lastColor;
            }
        } else {
            $applMessage->set('There are no projects with more than zero hours, that you are allowed to see.');
        }
    }
    
    require_once($config->finalizePage);
?>
