<?php
/**
 * 
 * $Id$
 * 
 */

require_once '../../../config.php';

require_once $config->classPath . '/modules/project/cache.php';
require_once 'HTTP/Header/Cache.php';
require_once 'HTML/TreeMenu.php';

$cacheKey = $userAuth->getData('id') . str_replace("/$lang/", '', $_SERVER['PHP_SELF']);
$httpCache = new HTTP_Header_Cache();
$httpCache->setHeader('Content-Type', 'text/javascript');
$tplFile = $layout->getContentTemplate(__FILE__);
$tpl->setOption('cacheFileExtension', 'js');
// we need to set that due to a bug in Xipe 1.7.3 which doesnt apply the xml-config properly
$tpl->setOption('locale', '');

if (modules_project_cache::needsRebuild($tplFile, $httpCache)) {        
    require_once $config->classPath . '/modules/project/tree.php';
    $projectTree = modules_project_tree::getInstance();
    //
    //  rebuild the tree
    //

    // get the id's we need to remove from the tree, those the user is not allowed to see
    $allIds = array();
    // not implemented yet, second parameter=0 shall get all children
//    $allIds = $projectTree->getChildrenIds(0, 0);  
    foreach ($projectTree->getNode() as $aNode) {
        $allIds[] = $aNode['id'];
    }
    $availIds = array();
    foreach ($projectsAvail = $projectTree->getAllAvailable() as $aProject) {
        $availIds[] = $aProject['id'];
    }
    $removeIds = array_diff($allIds, $availIds);

    // AK : switch DataSource is in Tree/Memory.php and has follwing definition :
    // function switchDataSource( $type , $dsn='' , $options=array() )
    // What the heck is that undefinded $trythis used for ????? -> php notice destryoing
    // jscript generation !
    //$projectTree->switchDataSource('Array', $tryThis);
    $projectTree->switchDataSource('ArrayFlat');
    $projectTree->setRemoveRecursively();
    foreach ($removeIds as $aId) {
        $projectTree->remove($aId);
    }
    $projectTree->setup();

    $rootId = $projectTree->getRootId();
    function _walkTree($curEl)
    {
        global $projectTree, $projectMember, $rootId, $pathsAsString;

        $id = $curEl['id'];
        $hasChildren = $projectTree->hasChildren($id);
        $pathsAsString[$id] = $projectTree->getPathAsString($id);
        $data = array();
        if ($id != $rootId) {
            $isMember = $projectMember->isMember($id);
            // show the icon depending on the user's rights
            $data['icon'] = 'folder'
                          . ($isMember?($projectMember->isManager($id)?'Manager':''):'Hidden')
                          . '.gif';
            // if the user is a memeber of the current project he can click it
            // if he is not, we show the node as 'NotSelectable' but not disabled, since
            // the user can unfold the tree here!
            $data['cssClass'] = 'treeMenu'
                              . ($isMember?'':($hasChildren?'NotSelectable':'Disabled'));
            if ($isMember) {
                $data['onclick'] = "projectTree.onClick($id,this.parentNode);";
            }
        }
        $projectTree->update($id,$data);
    }
    $projectTree->walk('_walkTree');
    $projectTree->setup();

    $icon = 'folder.gif';
    $options = array('images' => $config->vImgRoot . '/treeMenu');
    $_treeMenu = HTML_TreeMenu::createFromStructure(array(
        'structure' => $projectTree, 'type' => 'kriesing',
    ));
    $treeMenu = new HTML_TreeMenu_DHTML($_treeMenu, $options);

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

include $tpl->getCompiledTemplate();
