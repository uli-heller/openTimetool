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
$tplFile = 'common/js/projectTree.js.tpl';
$tpl->setOption('cacheFileExtension', 'js');
// we need to set that due to a bug in Xipe 1.7.3 which doesnt apply the xml-config properly
$tpl->setOption('locale', '');

if (modules_project_cache::needsRebuild($tplFile, $httpCache)) {
    require_once $config->classPath . '/modules/project/tree.php';
    require_once $config->classPath . '/modules/user/user.php';
    $projectTree = modules_project_tree::getInstance();
    // get the id's we need to remove from the tree, those the user is not allowed to see
    $allIds = array();
    // not implemented yet, second parameter=0 shall get all children
    //$allIds = $projectTree->getChildrenIds(0, 0);
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

    $user->reset();
    $user->setOrder('surname', true);
    $user->setLeftJoin(TABLE_PROJECTTREE2USER,
            'user_id=id AND projectTree_id IN (' . implode(',', $availIds) . ')');
    //$user->setGroup('id,projectTree_id');
    $users = $user->getAll();

    if (isset($users)) { // AK : avoid php notice
        switch ($config->teamcompressed) {
            case 2:
            case 3:
                /**
                 * No we collect the users in an array first. key is the projectTree_ID
                 * each array element is a $newEl and in name we collect all team members
                 * seperated with comma
                 */
                $usercoll = array();
                foreach ($users as $aUser) {
                    $parentId = $aUser['_projectTree2user_projectTree_id'];

                    if (isset($usercoll[$parentId])) {
                        $PM = ($aUser['_projectTree2user_isManager']) ? '[PM]' : '';
                        $usercoll[$parentId]['name'] .= ", {$aUser['name']} {$aUser['surname']} {$PM}";
                    } else {
                        $newEl = array(
                            '_teamMember' => true,
                            '_isManager'  => $aUser['_projectTree2user_isManager'],
                        );
                        $PM = ($aUser['_projectTree2user_isManager']) ? '[PM]' : '';
                        $newEl['name'] = "{$aUser['name']} {$aUser['surname']} {$PM}";
                        $usercoll[$parentId] = $newEl;
                    }
                }
                //var_dump($usercoll); die();
                foreach ($usercoll as $parentId => $el) {
                    $projectTree->add($el, $parentId, 0);
                }
                break;

            case 1:
                /**
                 * No we collect the users in 2 arrays first. key is the projectTree_ID
                 * each array element is a $newEl and in name we collect all team members
                 * seperated with comma
                 */
                $usercoll = array();
                $usercollPM = array();
                foreach ($users as $aUser) {
                    $parentId = $aUser['_projectTree2user_projectTree_id'];

                    if ($aUser['_projectTree2user_isManager']) {
                        if (isset($usercollPM[$parentId])) {
                            $usercollPM[$parentId]['name'] .= ", {$aUser['name']} {$aUser['surname']}";
                        } else {
                            $newEl = array(
                                '_teamMember' => true,
                                '_isManager'  => $aUser['_projectTree2user_isManager'],
                            );
                            $newEl['name'] = "{$aUser['name']} {$aUser['surname']}";
                            $usercollPM[$parentId] = $newEl;
                        }
                    } else {
                        if (isset($usercoll[$parentId])) {
                            $usercoll[$parentId]['name'] .= ", {$aUser['name']} {$aUser['surname']}";
                        } else {
                            $newEl = array(
                                '_teamMember' => true,
                                '_isManager'  => $aUser['_projectTree2user_isManager'],
                            );
                            $newEl['name'] = "{$aUser['name']} {$aUser['surname']}";
                            $usercoll[$parentId] = $newEl;
                        }
                    }
                }
                //var_dump($usercoll); var_dump($usercollPM); die();
                foreach ($usercoll as $parentId => $el) {
                    $projectTree->add($el, $parentId, 0);
                }
                foreach ($usercollPM as $parentId => $el) {
                    $projectTree->add($el, $parentId, 0);
                }
                break;

            case 0:
                // original code
                foreach ($users as $aUser) {
                    $newEl = array(
                        '_teamMember' => true,
                        '_isManager'  => $aUser['_projectTree2user_isManager'],
                    );
                    $newEl['name'] = "{$aUser['name']} {$aUser['surname']}";
                    $parentId = $aUser['_projectTree2user_projectTree_id'];
                    // the previousId (third parameter) is the same as the parentId, this way the 
                    // users are show right below the tree-node
                    $projectTree->add($newEl, $parentId, 0);
                }
                break;
        }
    }

    $projectTree->setup();

    $rootId = $projectTree->getRootId();
    function _walkTree($curEl)
    {
        global $projectTree, $projectMember, $rootId, $config;

        $id = $curEl['id'];
        $hasChildren = $projectTree->hasChildren($id);
        $pathAsString = $projectTree->getPathAsString($id);
        $data = array();
        if ($id != $rootId) {
            $isMember = $projectMember->isMember($id);
            if (isset($curEl['_teamMember'])) { // AK isset added
                $projectId = $projectTree->getParentId($id);
            } else {
                $projectId = $id;
            }
            $isManager = $projectMember->isManager($projectId);

            // show the icon depending on the user's rights
            if (isset($curEl['_teamMember'])) { // AK isset added
                if ($config->teamcompressed != 3) {
                    if ($config->teamcompressed == 2) {
                        $data['icon'] = 'icon'
                                      . ($isManager?'Manager':'User') . '.gif';
                    } else {
                        $data['icon'] = 'icon'
                                      . ($curEl['_isManager']?'Manager':'User')
                                      . '.gif';
                    }
                } else {
                    // the spartanic option with no icons at al
                    $data['icon'] = '';
                }

/* FIXXME do this one day
                if ($isManager) {
                    $removeImg = '<img src="' . $config->applPathPrefix . '/media/image/common/remove.gif" alt="remove">';
                    $removeLink = ' <a href="javascript://" onclick="removeAndConfirm()">' . $removeImg . '</a>';
                    $data['name'] = $removeLink . '&nbsp;' . $curEl['name'];
                }
*/
            } else {
                if ($config->teamcompressed != 3) {
                    $data['icon'] = 'folder'
                                  . ($isMember?($isManager?'Manager':''):'Hidden')
                                  . '.gif';
                } else {
                    // the spartanic option with no icons at all
                    $data['icon'] = '';
                }

                if ($isManager) {
                    $editImg = '<img src="'.$config->applPathPrefix.'/media/image/common/edit.gif" alt="edit">';
                    $editLink = ' <a href="?projectId='.$id.'">'.$editImg.'</a>';
                    $data['name'] = $curEl['name'].'&nbsp;'.$editLink;
                }
            }
            // if the user is a memeber of the current project he can click it
            // if he is not, we show the node as 'NotSelectable' but not disabled, since
            // the user can unfold the tree here!
            $data['cssClass'] = 'treeMenu'
                              . ($isMember?'':($hasChildren?'NotSelectable':'Disabled'));
        }
        $projectTree->update($id, $data);
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

    $tplFile = '/common/projectTree.js.tpl';
    include 'projectTree.js.php';
*/
    $tpl->forceRecache($tplFile);
}

// we need to send the cache-control headers!
$httpCache->sendHeaders();

$tpl->compile($tplFile);        
include $tpl->getCompiledTemplate();
