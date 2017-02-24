<?php
    //
    //  $Log: index.php,v $
    //  Revision 1.7  2003/02/13 16:19:03  wk
    //  - prevent no admins on this page
    //
    //  Revision 1.6  2002/11/30 13:04:50  wk
    //  - translate the next-prev stuff properly
    //
    //  Revision 1.5  2002/11/29 16:57:36  wk
    //  - call preset before using the user-object
    //
    //  Revision 1.4  2002/10/24 14:14:25  wk
    //  - use the saveHandler the new way. correctly now!
    //
    //  Revision 1.3  2002/09/02 11:29:15  wk
    //  - added previous next logic
    //
    //  Revision 1.2  2002/08/30 18:45:28  wk
    //  - implemented proper user editing
    //
    //  Revision 1.1  2002/08/20 09:02:57  wk
    //  - initial commit
    //
    //

	// as we dont have auto_prepend anymore, we have to include our config here
	require_once("../../../config.php");


    require_once $config->classPath.'/pageHandler.php';
    require_once 'vp/Application/HTML/NextPrev.php';
    
    if (!$user->isAdmin()) {
        require_once 'HTTP/Header.php';
        HTTP_Header::redirect($config->home);
    }

    if( isset($_REQUEST['removeId']) )	// AK: isset added
        $user->remove( $_REQUEST['removeId'] );

    $pageHandler->setObject($user);
    if( !$pageHandler->save( @$_REQUEST['newData'] ) )
    {
        $data = $pageHandler->getData();
        
        // if we are auth against LDAP we have to set a flag if we really edit an LDAP user
        // if not we must be able to modify the password !!
        $data['is_LDAP_user'] = false;
        if( $config->auth->method == 'LDAP' )
        {
            if(method_exists($userAuth,'is_LDAP_user')) {
        			if($userAuth->is_LDAP_user($data['login'])) {
		  				$data['is_LDAP_user'] = true;
		  			}
		  			else {
		  				$data['is_LDAP_user'] = false;
		  			}
		  	}
		}
    }

        
    $user->preset();
    $user->setWhere();
    $nextPrev = new vp_Application_HTML_NextPrev($user);
    $nextPrev->setLanguage( $lang );
    $users = $nextPrev->getData();


    require_once($config->finalizePage);

?>
