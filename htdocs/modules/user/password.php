<?php
    //
    //  $Log: password.php,v $
    //
    //  Revision 1.0  2002/08/20 09:02:57  wk
    //  - initial commit
    //
    // This one is for user to change password
    //

    require_once $config->classPath.'/pageHandler.php';
    require_once 'vp/Application/HTML/NextPrev.php';
    
    // we need that for retrieving the data of current user from DB
    // and to push it to form -> pagehandler->getData('id') 
	$userId = $userAuth->getData('id');
	$_REQUEST['id'] =$userId;  

    $pageHandler->setObject($user);
    $pageHandler->save( @$_REQUEST['newData'] );
    $data = $pageHandler->getData();
    	
           
    $user->preset();
    $user->setWhere('id='.$userAuth->getData('id'));  // only the user himselfs, not all ....
    $nextPrev = new vp_Application_HTML_NextPrev($user);
    $nextPrev->setLanguage( $lang );
	
	$users = $nextPrev->getData();
	//echo "users= ".print_r($users,true).'<br>';
	//echo "data= ".print_r($data,true).'<br>';

    require_once($config->finalizePage);

?>