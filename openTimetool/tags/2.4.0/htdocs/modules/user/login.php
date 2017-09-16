<?php
/**
 * 
 * $Id$
 * 
 */

require_once '../../../config.php';

require_once $config->classPath . '/mobile_browser.php';

if (isset($_REQUEST['logout']) && $_REQUEST['logout'] == 1) {
    $userAuth->logout();
}

if ($userAuth->isLoggedIn()) {
    require_once 'HTTP/Header.php';
    HTTP_Header::redirect($config->home);
} else {
    // remove all the temporary session data, mostly user specific,
    // like the filter-settings for the overview page
    unset($session->temp);
    /*
     * SX : not very nice, but when we call this mobile time page, we use a login-page without
     * any decorations. Just the pure 2 input fields. That is suitable and great for mobile access
     */
    $uri = trim($userAuth->getRequestedUrl());
    $browser = $_SERVER['HTTP_USER_AGENT'];
    if ((basename($uri) == 'mobile.php') || is_mobile($browser)) {
        // to get the page with all that stuff around
        $layout->setMainLayout('/modules/dialog');
    }

    if (isset($_REQUEST['loginFailed'])) {
        sleep(5);
        $applError->setOnce('Please enter a valid login!');
    }
}

$showLogin = ($account->isAspVersion() && $session->account->isActive) ||
        !$account->isAspVersion() ? true : false;

require_once $config->finalizePage;
