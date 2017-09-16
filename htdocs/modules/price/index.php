<?php
/**
 * 
 * $Id$
 * 
 */

require_once '../../../config.php';

require_once $config->classPath . '/modules/user/user.php';
require_once $config->classPath . '/modules/project/tree.php';
require_once $config->classPath . '/modules/task/task.php';
require_once $config->classPath . '/modules/price/price.php';
require_once $config->classPath . '/pageHandler.php';
require_once 'vp/Application/HTML/NextPrev.php';

if ($_REQUEST['removeId']) {
    $price->remove($_REQUEST['removeId']);
}

$pageHandler->setObject($price);
if (!$pageHandler->save($_REQUEST['newData'])) {
    $data = $pageHandler->getData();
}

// get the data for the add/edit fields
$users = $user->getAll();
$projectTree = modules_project_tree::getInstance(true);
$projects = $projectTree->getNode();
$tasks = $task->getAll();

// get all the prices for the overview
// sort them nicely by name
$price->setOrder('name');
$nextPrev = new vp_Application_HTML_NextPrev($price);
$prices = $nextPrev->getData();

require_once $config->finalizePage;
