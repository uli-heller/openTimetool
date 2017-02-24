<?php
    //
    //  $Log: projectTree.js.php,v $
    //  Revision 1.6.2.3  2003/04/10 18:05:06  wk
    //  - use references for getInstance!
    //
    //  Revision 1.6.2.2  2003/03/19 19:38:04  wk
    //  - remove some JS-calls which the projectTree-JS class handles now
    //
    //  Revision 1.6.2.1  2003/03/11 16:06:17  wk
    //  - make the caching language independent
    //
    //  Revision 1.6  2003/03/06 11:07:50  wk
    //  - use static tree-cache class
    //
    //  Revision 1.5  2003/03/05 19:40:02  wk
    //  - make them all work with the new HTML_TreeMenu interface
    //
    //  Revision 1.4  2003/03/04 19:11:15  wk
    //  - make the caching unique
    //
    //  Revision 1.3  2003/02/18 12:10:34  wk
    //  - remove unnecessary code
    //
    //  Revision 1.2  2003/02/10 16:17:04  wk
    //  - do properly caching
    //
    //  Revision 1.1  2003/02/05 19:01:21  wk
    //  - initial revision
    //
    
    require_once $config->classPath.'/modules/project/cache.php';
    require_once 'HTTP/Header/Cache.php';
    require_once 'HTML/TreeMenu.php';
    
    $cacheKey = $userAuth->getData('id').str_replace("/$lang/",'',$_SERVER['PHP_SELF']);
    $httpCache = new HTTP_Header_Cache();
    $httpCache->setHeader( 'Content-Type' , 'text/javascript' );
    $tplFile = $layout->getContentTemplate(__FILE__);
    $tpl->setOption('cacheFileExtension','js');
    $tpl->setOption('locale','');   // we need to set that due to a bug in Xipe 1.7.3 which doesnt apply the xml-config properly
    
    if (modules_project_cache::needsRebuild($tplFile,$httpCache)) {        
        require_once $config->classPath.'/modules/project/tree.php';
        $projectTree =& modules_project_tree::getInstance();
        //
        //  rebuild the tree
        //
        
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
        $projectTree->setup();

        $rootId = $projectTree->getRootId();
        function _walkTree($curEl)
        {
            global $projectTree,$projectMember,$rootId,$pathsAsString;

            $id = $curEl['id'];
            $hasChildren = $projectTree->hasChildren($id);
            $pathsAsString[$id] = $projectTree->getPathAsString($id);
            $data = array();
            if ($id!=$rootId) {
                $isMember = $projectMember->isMember($id);
                // show the icon depending on the user's rights
                $data['icon'] = 'folder'.($isMember?($projectMember->isManager($id)?'Manager':''):'Hidden').'.gif';
                // if the user is a memeber of the current project he can click it
                // if he is not, we show the node as 'NotSelectable' but not disabled, since
                // the user can unfold the tree here!
                $data['cssClass'] = 'treeMenu'.($isMember?'':($hasChildren?'NotSelectable':'Disabled'));
                if ($isMember) {
                    $data['onclick'] =  "projectTree.onClick($id,this.parentNode);";
                }
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
    */
        $tpl->forceRecache($tplFile);
    }
    // we need to send the cache-control  headers!
    $httpCache->sendHeaders();

    $tpl->compile($tplFile);        
    include($tpl->getCompiledTemplate());
?>