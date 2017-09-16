<?php
/**
 * 
 * $Id$
 * 
 */

require_once '../config.php';

//$_SERVER['VPCUST'] = 'demo1';

/*
require_once $config->classPath.'/modules/account/account.php';
require_once 'HTTP/Header.php';

$account->prepare();

$url = $config->vServerRoot . '/' . $session->account->ttVersion
     . str_replace("__{$session->accountName}", '', $_SERVER['PHP_SELF']);
*/

// by default we do simply redirect to the version 1.0.4
// the init.php in there checks if this is the proper version for this user
// if this is not the case the user will be redirected
$url = $config->vServerRoot . '/__' . $_SERVER['VPCUST'] . '/1.0.4/'
     . str_replace("/__{$_SERVER['VPCUST']}/", '', $_SERVER['PHP_SELF']);

//HTTP_Header::redirect($url);
header('Location: ' . $url);
