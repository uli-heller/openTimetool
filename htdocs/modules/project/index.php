<?php
    //
    //  $Id
    //
    // AK: added a lot of isset to avoid notices. Clicking project->modify the first time,
    // $_REQUEST is simple empty

    require_once('vp/Application/HTML/Tree.php');
    require_once($config->classPath.'/modules/project/tree.php');
    $projectTree =& modules_project_tree::getInstance();


    if (isset($_REQUEST['tree']['add']) && is_array($_REQUEST['tree']['add'])) {
        $_REQUEST['tree']['add']['startDate'] = $util->makeTimestamp( $_REQUEST['tree']['add']['startDate'] );
        $_REQUEST['tree']['add']['endDate'] = $util->makeTimestamp( $_REQUEST['tree']['add']['endDate'] );
        // write null in the table, so there is really no value it wont confuse the user
        // and we can still use 0
        if ($_REQUEST['tree']['add']['close'] == '') {
            $_REQUEST['tree']['add']['close'] = null;
        }
        if ($_REQUEST['tree']['add']['roundTo'] == '') {
            $_REQUEST['tree']['add']['roundTo'] = null;
        }
        if ($_REQUEST['tree']['add']['maxDuration'] == '') {
            $_REQUEST['tree']['add']['maxDuration'] = null;
        }
    }
    if (isset($_REQUEST['tree']['update']) && is_array($_REQUEST['tree']['update'])) {
        $_REQUEST['tree']['update']['startDate'] = $util->makeTimestamp( $_REQUEST['tree']['update']['startDate'] );
        $_REQUEST['tree']['update']['endDate'] = $util->makeTimestamp( $_REQUEST['tree']['update']['endDate'] );
        // write null in the table, so there is really no value it wont confuse the user
        // and we can still use 0
        if ($_REQUEST['tree']['update']['close'] == '') {
            $_REQUEST['tree']['update']['close'] = null;
        }
        if ($_REQUEST['tree']['update']['roundTo'] == '') {
            $_REQUEST['tree']['update']['roundTo'] = null;
        }
        if ($_REQUEST['tree']['update']['maxDuration'] == '') {
            $_REQUEST['tree']['update']['maxDuration'] = null;
        }
        if (!$projectTree->checkBeforeUpdate($_REQUEST['tree']['update'])) {
            $editFolder = $_REQUEST['tree']['update'];
            unset($_REQUEST['tree']['update']);
        }
    }
    
    // AK added @ due to notices; if param is empty, function returns null
    // AK : very strange ! This class got never instantiated but tries to use $this inside !?!?!
    // AK : That can't work ! At least not on php5 !!
    // vp_Application_HTML_Tree::handle(@$_REQUEST['tree'],$projectTree);
    // AK we instantiate now and put it global
    if(!isset($vp_Application_HTML_Tree))
    	$vp_Application_HTML_Tree = new vp_Application_HTML_Tree;
    $vp_Application_HTML_Tree->handle(@$_REQUEST['tree'],$projectTree); 
    // AK end
    $allFolders = $projectTree->getAll();

    if (isset($_REQUEST['id'])) {
        $editFolder = $projectTree->getElement($_REQUEST['id']);
    }

//    $allFolders = $projectTree->getAll();
    $allVisibleFolders = $projectTree->getAllVisible();

    require_once($config->finalizePage);
?>
