<?php
/**
 * 
 * $Id$
 * 
 */

require_once '../../../config.php';

require_once $config->classPath . '/pageHandler.php';
require_once 'vp/Application/HTML/NextPrev.php';

if (!$user->isAdmin()) {
    require_once 'HTTP/Header.php';
    HTTP_Header::redirect($config->home);
}

// AK: isset added
if (isset($_REQUEST['removeId'])) {
    if ($config->demoMode) {
        $applMessage->set('Please note! This function is disabled in the demo version.');
    } else {
        $user->remove($_REQUEST['removeId']);
    }
}

$pageHandler->setObject($user);
if (!empty($_REQUEST['newData']['id']) && $config->demoMode) {
    $applMessage->set('Please note! This function is disabled in the demo version.');
} else {
    if (!$pageHandler->save(@$_REQUEST['newData'])) {
        $data = $pageHandler->getData();

        // if we are auth against LDAP we have to set a flag if we really edit an LDAP user
        // if not we must be able to modify the password !!
        $data['is_LDAP_user'] = false;
        if ($config->auth->method == 'LDAP') {
            if (method_exists($userAuth, 'is_LDAP_user')) {
                if ($userAuth->is_LDAP_user($data['login'])) {
                    $data['is_LDAP_user'] = true;
                } else {
                    $data['is_LDAP_user'] = false;
                }
            }
        }
    }
}

$user->preset();
$user->setWhere();
$nextPrev = new vp_Application_HTML_NextPrev($user);
$nextPrev->setLanguage($lang);
$users = $nextPrev->getData();

require_once $config->finalizePage;
