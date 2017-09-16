<?php
/**
 * 
 * $Id$
 * 
 */

// SX Nov 2012 : xajax
require_once $config->applRoot . '/xajax/xajax_core/xajax.inc.php';
require_once $config->applRoot . '/modules/xajax_if.php';

$_htmlTitle = strip_tags($config->applName);

$_bodyClass = '';
if (isset($GLOBALS['bodyClass'])) {
    $_bodyClass = 'class="' . htmlentities($GLOBALS['bodyClass']) . '"';
}

require_once $config->finalizePage;
