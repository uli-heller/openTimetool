<?php
/**
 * 
 * $Id$
 * 
 */

require_once '../../../config.php';

require_once $config->classPath . '/pageHandler.php';
require_once $config->classPath . '/modules/task/task.php';
require_once 'vp/Application/HTML/NextPrev.php';

if (!$user->isAdmin()) {
    require_once 'HTTP/Header.php';
    HTTP_Header::redirect($config->home);
}

// AK : isset added
if (isset($_REQUEST['removeId'])) {
    $task->remove((int) $_REQUEST['removeId']);
}

$pageHandler->setObject($task);
// AK : "!isset($_REQUEST['newData']) &&"  added
if (!$pageHandler->save(@$_REQUEST['newData'])) {
    $data = $pageHandler->getData();
}

$nextPrev = new vp_Application_HTML_NextPrev($task);
$nextPrev->setLanguage($lang);
$tasks = $nextPrev->getData();

require_once $config->finalizePage;
