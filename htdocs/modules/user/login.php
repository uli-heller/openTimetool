<?php
    //
    //  $Log: login.php,v $
    //  Revision 1.8  2003/01/30 16:13:29  wk
    //  - fix showLogin
    //
    //  Revision 1.7  2003/01/29 16:06:32  wk
    //  - show login only when accoun tis active
    //
    //  Revision 1.6  2003/01/28 19:22:23  wk
    //  - E_ALL stuff
    //
    //  Revision 1.5  2002/10/22 14:42:33  wk
    //  - changed $auth to $userAuth
    //
    //  Revision 1.4  2002/08/29 13:30:47  wk
    //  - remove settings from session if user has logged out and also to be sure that they are remove when user is already logged out
    //
    //  Revision 1.3  2002/08/20 09:03:19  wk
    //  - remove session->temp on logout
    //
    //  Revision 1.2  2002/07/25 10:10:32  wk
    //  - show login form only when user is not logged in
    //
    //  Revision 1.1.1.1  2002/07/22 09:37:37  wk
    //
    //
    //
    
	require_once $config->classPath.'/mobile_browser.php';
                       
    if (isset($_REQUEST['logout']) && $_REQUEST['logout']== 1) {
        $userAuth->logout();
    }

    if ($userAuth->isLoggedIn()) {
        require_once 'HTTP/Header.php';
        HTTP_Header::redirect($config->home);
    } else {
        unset($session->temp);      // remove all the temporary session data, mostly user specific, like the filter-settings for the overview page
        /*
         * SX : not very nice, but when we call this mobile time page, we use a login-page without
         * any decorations. Just the pure 2 input fields. That is suitable and great for mobile access
         */
        $uri =  trim($userAuth->getRequestedUrl());
        $browser = $_SERVER['HTTP_USER_AGENT'];
   		if((basename($uri) == 'mobile.php') || is_mobile($browser)) {
    		$layout->setMainLayout('/modules/dialog');   // to get the page with all that stuff around
    	}          
    }

    $showLogin = ($account->isAspVersion() && $session->account->isActive)||!$account->isAspVersion()?true:false;

    require_once $config->finalizePage;

?>
