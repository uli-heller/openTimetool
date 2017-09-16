<?php
/**
 * 
 * $Id$
 * 
 */

require_once '../../../config.php';

// AK : added @ to avoid notice
if (@$_REQUEST['adminModeOn']) {
    $user->adminModeOn();
} else {
    $user->adminModeOff();
}

$isAdmin = $user->isAdmin();

require_once $config->finalizePage;
