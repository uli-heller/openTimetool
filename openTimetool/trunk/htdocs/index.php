<?php
    /**
    *
    *  	$Id
    * 
    *	When logging in by http://<server>/openTimetool or http://<server>/openTimetool/index.php
    *	we run through here, check if we are really authenticated and redirect then to the initial
    *	page after log in : today.php	
    * 	Well we come here as well when "today" is selected from menu of course ;-9 ...
    *
    * ************* switch to SVN **************
    *  $Log: index.php,v $
    *  Revision 1.6  2003/01/29 10:41:50  wk
    *  - fix account handling
    *
    *  Revision 1.5  2003/01/28 10:59:19  wk
    *  - use HTTP_Header for redirecting
    *
    *  Revision 1.4  2002/11/29 17:00:08  wk
    *  - get account name by get too
    *
    *  Revision 1.3  2002/11/29 16:59:21  wk
    *  - do asp-account request
    *
    *  Revision 1.2  2002/07/25 10:12:00  wk
    *  - forward to time page
    *
    *  Revision 1.1.1.1  2002/07/22 09:37:37  wk
    *
    *
    */
   
    if (@$_REQUEST['resetAccountName']) {
        $account->setAccountName();      
    }

    if (@$_REQUEST['newData']['accountName']) {
        require_once $config->classPath.'/modules/remote/remote.php';

        $accountName = $_REQUEST['newData']['accountName'];
        if (modules_remote::execute('account.isAccountName',$accountName)) {
            $account->setAccountName($accountName);
            $account->prepare(true);    // force getting the data via XML-RPC again
        } else {
            $applError->set('This is not a valid account name!');
        }
    }

    if( ($account->isAspVersion() && $account->getAccountName()) || !$account->isAspVersion() ) {
        require_once 'HTTP/Header.php';
        // A.Kejr : 
        // we get here after running through openTimetool/config.php (includes
        // openTimetool/init.php) started by .htaccess in openTimetool/htdocs !!
        // central point here when starting !!!!!!!!!!!!!!!!!
        //var_dump($config);echo "<br>stop".$config->vApplRoot."<p>";die();
        HTTP_Header::redirect($config->vApplRoot.'/modules/time/today.php');  
    }
    
    // this we only do for the ASP version
    
    $applMessage->set('Please enter your account name!');

    require_once($config->finalizePage);

?>
