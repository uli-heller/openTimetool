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

// the cache key is the user-id since every user ahs different right on each project 
// and so the folders need to be shown differently, even though they are admins
$cacheKey = $userAuth->getData('id') . str_replace("/$lang/", '', $_SERVER['PHP_SELF']);
$httpCache = new HTTP_Header_Cache();
$httpCache->setHeader('Content-Type', 'text/javascript');
$tplFile = 'common/js/projectTree.js.tpl';    
$tpl->setOption('cacheFileExtension', 'js');
// we need to set that due to a bug in Xipe 1.7.3 which doesnt apply the xml-config properly
$tpl->setOption('locale', '');

if (modules_project_cache::needsRebuild($tplFile,$httpCache)) {
    require_once $config->classPath . '/modules/project/tree.php';
    $projectTree = modules_project_tree::getInstance();

    // AK : switch DataSource is in Tree/Memory.php and has follwing definition :
    // function switchDataSource( $type , $dsn='' , $options=array() )
    // What the heck is that undefinded $trythis used for ????? -> php notice destryoing
    // jscript generation !
    //$projectTree->switchDataSource('Array', $tryThis);
    $projectTree->switchDataSource('ArrayFlat');

    $rootId = $projectTree->getRootId();
    function _walkTree($curEl)
    {
        global $projectTree, $projectMember, $rootId, $pathsAsString;

        $id = $curEl['id'];
        $hasChildren = $projectTree->hasChildren($id);
        $pathsAsString[$id] = $projectTree->getPathAsString($id);
        $data = array();
        if ($id != $rootId) {
            $data['icon'] = 'folder'
                          . ($projectMember->isManager($id)?'Manager':'')
                          . '.gif';
            // if the user is a memeber of the current project he can click it
            // if he is not, we show the node as 'NotSelectable' but not disabled, since
            // the user can unfold the tree here!
            $data['cssClass'] = 'treeMenu';
            $data['onclick'] = "projectTree.onClick($id,this.parentNode);";
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

    $tpl->forceRecache($tplFile);
}

// we need to send the cache-control headers!
$httpCache->sendHeaders();

$tpl->compile($tplFile);
include $tpl->getCompiledTemplate();
