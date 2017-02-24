<?php
    //
    //  $Log: member.php,v $
    //  Revision 1.8  2003/03/04 19:15:39  wk
    //  - editing members is now fully JS
    //
    //  Revision 1.7  2003/02/18 12:12:01  wk
    //  - let admin edit other's project's teams too
    //
    //  Revision 1.6  2003/02/13 16:17:44  wk
    //  - prevent E_ALL warnings
    //
    //  Revision 1.5  2003/02/11 17:56:31  wk
    //  - allow not managers here too, really
    //
    //  Revision 1.4  2003/02/11 17:54:55  wk
    //  - allow not managers here too
    //
    //  Revision 1.3  2003/02/10 19:14:02  wk
    //  - use projectTreeDyn now
    //
    //  Revision 1.2  2002/11/13 19:01:03  wk
    //  - use method isAdmin()
    //
    //  Revision 1.1  2002/10/28 11:19:43  wk
    //  - for editing project members
    //
    //

    require_once $config->classPath.'/modules/project/treeDyn.php';
    require_once $config->classPath.'/modules/project/member.php';
    require_once $config->classPath.'/modules/user/user.php';


    $isAdmin = $user->isAdmin();
    // AK : added @ everywhere to avoid php notices ! if all fails $projectid is unset ...
    $projectId = @$_GET['projectId'] ? @$_GET['projectId'] : @$_POST['projectId'];
	if(!$projectId && isset($_REQUEST['projectId'])) $projectId = $_REQUEST['projectId'];

    // do only allow manager and admins here!
    if (!$isAdmin && $projectId && !$projectMember->isManager($projectId)) {
        require_once 'HTTP/Header.php';
        HTTP_Header::redirect();
    }
	

	// AK : use isset instead of just checking if true ...
    if (isset($_POST['action_save'])) {
    	/**
    	 * If team inheritance is selected, we'll retrieve the members from parent project 
    	 * and add them here. 
    	 */
    	if (isset($_REQUEST['InheritTeam'])) {
			$parentId = $projectTreeDyn->getParentId($projectId);

        	$user->reset();
	        // select only what we really need
    	    $user->setSelect('id,name,surname');
        	$user->addSelect(TABLE_PROJECTTREE2USER.'.id');
	        $user->addSelect(TABLE_PROJECTTREE2USER.'.isManager as isManager');
    	    $user->addSelect(TABLE_PROJECTTREE2USER.'.projectTree_id as projectTree_id');      
        	$user->setLeftJoin(TABLE_PROJECTTREE2USER,'user_id='.TABLE_USER.'.id AND projectTree_id='.$parentId );
	        $users = $user->getAll();
	        // debug :
			//$applError->set(print_r($users,true).'<br>');	        
	        $managers = array();
    	    $members = array();
        	foreach ($users as $key=>$aUser) {
            	if ($aUser['isManager']) {
                	$managers[] = $aUser['id'];
	                unset($users[$key]);
    	        } elseif( $aUser['projectTree_id'] == $parentId ) {
        	        $members[] = $aUser['id'];
            	    unset($users[$key]);
	            }
    	    }
    	    // debug
			//$applError->set(print_r($managers,true).'<br>');
			//$applError->set(print_r($members,true).'<br>');


        	if ($projectMember->updateSpecial(@$members,@$managers,@$projectId)) {
            	unset($projectId);
        	}    		
		// EOF team inheritance
		}  else  {  
			  	
        	if ($projectMember->updateSpecial(@$_POST['members'],@$_POST['managers'],@$projectId)) {
            	unset($projectId);
        	}
    	}
    }

    if (isset($projectId)) {
        $curProject = $projectTreeDyn->getPathAsString( $projectId );

		/**
		 * SX : Get the parent project
		 */
		//$parentId = $projectTreeDyn->getParentId($projectId);
		$tmpsplit = explode(' | ',$curProject);
		if(!is_array($tmpsplit) || count($tmpsplit)==1) {
			$parentProject = $curProject;
		} else {
			$parentProject = $tmpsplit[count($tmpsplit)-2];
		}

        $user->reset();
        // select only what we really need
        $user->setSelect('id,name,surname');
        $user->addSelect(TABLE_PROJECTTREE2USER.'.id');
        $user->addSelect(TABLE_PROJECTTREE2USER.'.isManager as isManager');
        $user->addSelect(TABLE_PROJECTTREE2USER.'.projectTree_id as projectTree_id');      
        $user->setLeftJoin(TABLE_PROJECTTREE2USER,'user_id='.TABLE_USER.'.id AND projectTree_id='.$projectId );
		// AK: order that stuff by surname,prename 
        $user->setOrder('surname,name');
        $users = $user->getAll();
        $selectSizeUsers = sizeof($users)<10 ? 10 : 20;
        $selectSizeMembers = sizeof($users)>10 ? 10 : 5;
        $selectSizeManagers = 5;

        $managers = array();
        $members = array();
        foreach ($users as $key=>$aUser) {
            if ($aUser['isManager']) {
                $managers[] = $aUser;
                unset($users[$key]);
            } elseif( $aUser['projectTree_id'] == $projectId ) {
                $members[] = $aUser;
                unset($users[$key]);
            }
        }
    }
    
    $projectTreeJsFile = 'projectTreeTeam'.($isAdmin?'Admin':'');

    require_once($config->finalizePage);

?>
