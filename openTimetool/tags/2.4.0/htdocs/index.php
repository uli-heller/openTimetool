<?php
/**
 * 
 * $Id$
 * 
 * When logging in by http://<server>/openTimetool or http://<server>/openTimetool/index.php
 * we run through here, check if we are really authenticated and redirect then to the initial
 * page after log in : today.php
 * Well we come here as well when "today" is selected from menu of course ;-9 ...
 * 
 */

require_once '../config.php';

if (!isset($account) || !isset($config)) {
    // shouldnt happen anymore as we dont use auto_prepend anymore
    print_r("Something terribly went wrong with auto_prepend. Maybe Suhosin or PHP as FastCGI ? See README ...");
    //require("/srv/www/system-worx.de/pm/openTimetool/config.php");
    die();
}

if (isset($_REQUEST['resetAccountName'])) {
    $account->setAccountName();
}

if (isset($_REQUEST['newData']['accountName'])) {
    require_once $config->classPath . '/modules/remote/remote.php';

    $accountName = $_REQUEST['newData']['accountName'];
    if (modules_remote::execute('account.isAccountName', $accountName)) {
        $account->setAccountName($accountName);
        $account->prepare(true); // force getting the data via XML-RPC again
    } else {
        $applError->set('This is not a valid account name!');
    }
}

if (($account->isAspVersion() && $account->getAccountName()) ||
        !$account->isAspVersion()) {
    require_once 'HTTP/Header.php';
    // A.Kejr :
    // we get here after running through openTimetool/config.php (includes
    // openTimetool/init.php) started by .htaccess in openTimetool/htdocs !!
    // central point here when starting !!!!!!!!!!!!!!!!!
    //var_dump($config);echo "<br>stop".$config->vApplRoot."<p>";die();
    HTTP_Header::redirect($config->vApplRoot . '/modules/time/today.php');  
}

// this we only do for the ASP version

$applMessage->set('Please enter your account name!');

require_once $config->finalizePage;
