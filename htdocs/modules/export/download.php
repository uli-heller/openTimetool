<?php
/**
 * 
 * $Id$
 * 
 */

require_once '../../../config.php';

require_once $config->classPath . '/modules/export/export.php';

if (!isset($_REQUEST['id']) ) {
    require_once 'HTTP/Header.php';
    HTTP_Header::redirect('index.php');
}

$export->putFile((int) $_REQUEST['id'], @$_REQUEST['download']);
