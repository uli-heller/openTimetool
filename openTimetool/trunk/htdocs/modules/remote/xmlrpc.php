<?php
/**
 * 
 * $Id$
 * 
 */

require_once '../../../config.php';

require_once $config->classPath . '/modules/time/time.php';
require_once $config->classPath . '/modules/project/tree.php';

require 'XML/RPC/Server.php';

/**
 *
 *   @param  string  the account name
 *   @return mixed   either an array with all the data or false if accountName was not found
 *                   or no data were found for the account name
 */
function getProject($passedParas)
{
    global $time;

    $projectId = XML_RPC_decode($passedParas->getParam(0));

    $time->reset();
    $time->autoJoin(TABLE_TASK);

    if ($projectId) {
        $time->setWhere('projectTree_id=' . $projectId);
    }
    $time->addWhere(TABLE_TASK . '.calcTime=1');
    $time->setOrder('timestamp', true);

    $times = $time->getAll();
    if ($times) {
        foreach ($times as $k => $aTime) {
            foreach ($aTime as $key => $val) {
                $times[$k][$key] = utf8_encode(htmlspecialchars($val));
            }
        }
    }

    return XML_RPC_encode($times);
}

function project_getAllAvailable()
{
    $projectTree = modules_project_tree::getInstance(true);
    $_projects = $projectTree->getAllAvailable();
    $projects = array();
    if ($_projects) {
        foreach ($projects as $k => $aProject) {
            foreach ($aProject as $key => $val) {
                if (!is_array($val)) {
                    $projects[$k][$key] = utf8_encode(htmlspecialchars($val));
                }
            }
        }
    }

    return XML_RPC_encode($times);
}

function time_getFiltered($passedParas)
{
    global $time;

    $filter = XML_RPC_decode($passedParas->getParam(0));

    $times = $time->getFiltered($filter, true);
    if ($times) {
        foreach ($times as $k => $aTime) {
            foreach ($aTime as $key => $val) {
                $times[$k][$key] = utf8_encode(htmlspecialchars($val));
            }
        }
    }

    return XML_RPC_encode($times);
}

$methods = array(
    'project.get' => array(
        'function'  => 'getProject',
        //'signature' => array(array($GLOBALS['XML_RPC_String'], $GLOBALS['XML_RPC_String'])),
        'docstring' => '',
    ),
    'project.getAllAvailable' => array(
        'function'  => 'project_getAllAvailable',
        //'signature' => array(array($GLOBALS['XML_RPC_String'], $GLOBALS['XML_RPC_String'])),
        'docstring' => '',
    ),
    'time.getFiltered' => array(
        'function'  => 'time_getFiltered',
        //'signature' => array(array($GLOBALS['XML_RPC_String'], $GLOBALS['XML_RPC_String'])),
        'docstring' => '',
    ),
/*
    'account.isActive' => array(
        'function'  => 'isAccountActive',
        //'signature' => array(array($GLOBALS['XML_RPC_String'], $GLOBALS['XML_RPC_String'])),
        'docstring' => '',
    ),
*/
);

$server = new XML_RPC_Server($methods);
