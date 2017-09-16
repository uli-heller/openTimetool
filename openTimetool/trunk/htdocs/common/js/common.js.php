<?php
/**
 * 
 * $Id$
 * 
 */

require_once '../../../config.php';

require_once 'HTTP/Header/Cache.php';
$httpCache = new HTTP_Header_Cache();
$httpCache->setHeader('Content-Type', 'text/javascript');

$tplFile = $layout->getContentTemplate(__FILE__);
$tpl->compile($tplFile);

$httpCache->exitIfCached( !$tpl->compiled() );
// the call above sets headers too, so call sendHeaders here!
$httpCache->sendHeaders();

include $tpl->getCompiledTemplate();
