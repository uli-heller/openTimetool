<?php
/**
 * 
 * $Id$
 * 
 */

require_once '../../../config.php';

require_once $config->classPath . '/pageHandler.php';
require_once 'vp/Application/HTML/NextPrev.php';

// we need that for retrieving the data of current user from DB
// and to push it to form -> pagehandler->getData('id')
$userId = $userAuth->getData('id');
$_REQUEST['id'] = $userId;

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

$pageHandler->setObject($user);
if (!empty($_REQUEST['newData']) && $config->demoMode) {
    $applMessage->set('Please note! This function is disabled in the demo version.');
} else {
    $pageHandler->save(@$_REQUEST['newData']);
}
$data = $pageHandler->getData();

$user->preset();
// only the user himselfs, not all ....
$user->setWhere('id=' . $userAuth->getData('id'));
$nextPrev = new vp_Application_HTML_NextPrev($user);
$nextPrev->setLanguage($lang);

$users = $nextPrev->getData();
//echo 'users= ' . print_r($users, true) . '<br>';
//echo 'data= ' . print_r($data, true) . '<br>';

require_once $config->finalizePage;
