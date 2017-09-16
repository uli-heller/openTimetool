<?php
/**
 * 
 * $Id$
 * 
 */

require_once '../../../config.php';

require_once $config->classPath . '/modules/OOoTemplate/OOoTemplate.php';

if (!$_REQUEST['id']) {
    header('Location: index.php');
    die();
}

$OOoTemplate->putFile((int) $_REQUEST['id']);
