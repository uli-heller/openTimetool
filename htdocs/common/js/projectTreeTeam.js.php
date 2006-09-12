<?php
    //
    //  $Log: projectTreeTeam.js.php,v $
    //  Revision 1.7.2.2  2003/04/10 18:05:06  wk
    //  - use references for getInstance!
    //
    //  Revision 1.7.2.1  2003/03/11 16:06:17  wk
    //  - make the caching language independent
    //
    //  Revision 1.7  2003/03/06 13:22:47  wk
    //  - we dont need no onCLick here
    //
    //  Revision 1.6  2003/03/06 11:07:50  wk
    //  - use static tree-cache class
    //
    //  Revision 1.5  2003/03/05 19:40:02  wk
    //  - make them all work with the new HTML_TreeMenu interface
    //
    //  Revision 1.4  2003/03/04 19:29:07  wk
    //  - remove JS debug message
    //
    //  Revision 1.3  2003/03/04 19:11:15  wk
    //  - make the caching unique
    //
    //  Revision 1.2  2003/02/18 10:49:25  wk
    //  - making the cache work properly and plaing with the admin mode file
    //
    //  Revision 1.1  2003/02/10 12:08:02  wk
    //  - initial commit
    //
    //  Revision 1.1  2003/02/05 19:01:21  wk
    //  - initial revision
    //
    //

    require_once $config->classPath.'/modules/project/cache.php';
    require_once 'HTTP/Header/Cache.php';
    require_once 'HTML/TreeMenu.php';    

    $cacheKey = $userAuth->getData('id').str_replace("/$lang/",'',$_SERVER['PHP_SELF']);
    $httpCache = new HTTP_Header_Cache();
    $httpCache->setHeader( 'Content-Type' , 'text/javascript' );
    $tplFile = 'common/js/projectTree.js.tpl';
    $tpl->setOption('cacheFileExtension','js');
    $tpl->setOption('locale','');   // we need to set that due to a bug in Xipe 1.7.3 which doesnt apply the xml-config properly
    
    if (modules_project_cache::needsRebuild($tplFile,$httpCache)) {
        require_once $config->classPath.'/modules/project/tree.php';
        require_once $config->classPath.'/modules/user/user.php';
        $projectTree =& modules_project_tree::getInstance();
        // get the id's we need to remove from the tree, those the user is not allowed to see
        $allIds = array();
    //    $allIds = $projectTree->getChildrenIds(0,0);  not implemented yet, second parameter=0 shall get all children
        foreach ($projectTree->getNode() as $aNode) {
            $allIds[] = $aNode['id'];
        }
        $availIds = array();
        foreach ($projectsAvail = $projectTree->getAllAvailable() as $aProject) {
            $availIds[] = $aProject['id'];
        }
        $removeIds = array_diff($allIds,$availIds);
		// AK : switch DataSource is in Tree/Memory.php and has follwing definition : 
		// function switchDataSource( $type , $dsn='' , $options=array() )
		// What the heck is that undefinded $trythis used for ????? -> php notice destryoing
		// jscript generation !
        // $projectTree->switchDataSource('Array',$tryThis);
        $projectTree->switchDataSource('Array');
        $projectTree->setRemoveRecursively();
        foreach ($removeIds as $aId) {
            $projectTree->remove($aId);
        }

        $user->reset();
        $user->setOrder('surname',true);
        $user->setLeftJoin(TABLE_PROJECTTREE2USER,'user_id=id AND projectTree_id IN('.implode(',',$availIds).')');
    //    $user->setGroup('id,projectTree_id');
        $users = $user->getAll();

    //    $myProjects = $projectMember->getMemberProjects();
    //    foreach($myProjects as $aProject) {
    //        print "{$aProject['id']} : {$aProject['projectTree_id']} manager? {$aProject['isManager']}\r\n";
        foreach ($users as $aUser) {
            $newEl = array('_teamMember'=>true,'_isManager'=>$aUser['_projectTree2user_isManager']);
            $newEl['name'] = "{$aUser['name']} {$aUser['surname']}";
            $parentId = $aUser['_projectTree2user_projectTree_id'];
            // the previousId (third parameter) is the same as the parentId, this way the 
            // users are show right below the tree-node
            if ($projectMember->isMember($parentId)) {
                $projectTree->add($newEl,$parentId,0);
            }
        }

        $projectTree->setup();

        $rootId = $projectTree->getRootId();
        function _walkTree($curEl)
        {
            global $projectTree,$projectMember,$rootId,$config;

            $id = $curEl['id'];
            $hasChildren = $projectTree->hasChildren($id);
            $pathAsString = $projectTree->getPathAsString($id);
            $data = array();
            if ($id!=$rootId) {
                $isMember = $projectMember->isMember($id);
                //
                if (isset($curEl['_teamMember'])) {   // AK isset added
                    $projectId = $projectTree->getParentId($id);
                } else {
                    $projectId = $id;
                }
                $isManager = $projectMember->isManager($projectId);

                // show the icon depending on the user's rights
                if (isset($curEl['_teamMember'])) {     // AK isset added
                    $data['icon'] = 'icon'.($curEl['_isManager']?'Manager':'User').'.gif';
    /* FIXXME do this one day
                    if ($isManager) {
                        $removeImg = '<img src="'.$config->applPathPrefix.'/media/image/common/remove.gif" border="0"/>';
                        $removeLink = ' <a href="javascript://" onClick="removeAndConfirm()">'.$removeImg.'</a>';
                        $data['name'] = $removeLink.'&nbsp;'.$curEl['name'];
                    }
    */
                } else {
                    $data['icon'] = 'folder'.($isMember?($isManager?'Manager':''):'Hidden').'.gif';
                    if ($isManager) {
                        $editImg = '<img src="'.$config->applPathPrefix.'/media/image/common/edit.gif" border="0"/>';
                        $editLink = ' <a href="?projectId='.$id.'">'.$editImg.'</a>';
                        $data['name'] = $curEl['name'].'&nbsp;'.$editLink;
                    }
                }
                // if the user is a memeber of the current project he can click it
                // if he is not, we show the node as 'NotSelectable' but not disabled, since
                // the user can unfold the tree here!
                $data['cssClass'] = 'treeMenu'.($isMember?'':($hasChildren?'NotSelectable':'Disabled'));
            }
            $projectTree->update($id,$data);
        }
        $projectTree->walk('_walkTree');
        $projectTree->setup();

        $icon = 'folder.gif';
        $options = array('images'=>$config->vImgRoot.'/treeMenu');
        $_treeMenu = HTML_TreeMenu::createFromStructure(array('structure'=>$projectTree,'type'=>'kriesing'));
        $treeMenu = new HTML_TreeMenu_DHTML($_treeMenu,$options);

    /*
        $treeJs = preg_replace("~(\s{2})~",'',$treeMenu->toHtml());
        $treeJs = str_replace('"','\\"',$treeJs);
    //    $treeJs = str_replace("\n","\\n",$treeJs);
    //    $treeJs = str_replace("\r","\\r",$treeJs);
        $treeJs = str_replace("\r\n","",$treeJs);

        $tplFile = '/common/projectTree.js.tpl';
        include 'projectTree.js.php';
    */
        $tpl->forceRecache($tplFile);
    }
    // we need to send the cache-control  headers!
    $httpCache->sendHeaders();

    $tpl->compile($tplFile);        
    include($tpl->getCompiledTemplate());
?>
