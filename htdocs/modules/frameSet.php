<?php
/**
 * 
 * $Id$
 * 
 */

$session->layout = 'framedDefault';
$layout->setLayout('framedDefault');

$tpl->compile($layout->getContentTemplate());
// and include the compiled main template
include $tpl->compiledTemplate;
