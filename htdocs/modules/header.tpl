<!--

$id$

-->

<!-- include the common macro so we can use common_getJs -->
{%include common/macro/common.mcr%}

<!DOCTYPE html>
<html lang="{$lang}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="openTimetool, Webbasierte Projektzeiterfassung, web based project time tracking">
    <meta name="abstract" content="openTimetool, Webbasierte Projektzeiterfassung, web based project time tracking">
    <meta name="keywords" content="Webbasierte Projektzeiterfassung, web based project time tracking, Zeitmanagement, Zeiterfassung, time tracker, time management">
    <meta name="robots" content="index, follow">
    <meta name="revisit-after" content="10 days">
    <meta name="author" content="openTimetool">
    <meta name="publisher" content="openTimetool">
    <meta name="copyright" content="openTimetool">
    <meta name="owner" content="openTimetool">
    <meta http-equiv="pragma" content="no-cache">

    {if($pageProp->get('pageHeader'))}
        <title>{echo strip_tags($T_pageProp->get('pageHeader'))} &middot; {$_htmlTitle}</title>
    {else}
        <title>{$_htmlTitle}</title>

<!-- use getOption here!!! is this not implemented??? -->
    {if ($userAuth->isLoggedIn())} <!-- we dont need to reload if the user is not logged in -->
        <meta http-equiv="refresh" content="{$userAuth->options['expire']+5};url={$_SERVER['PHP_SELF']}">

    <link rel="stylesheet" type="text/css" href="{$layout->getCssFile()}">
    <link rel="stylesheet" type="text/css" href="{$config->applPathPrefix}/external/calendar/popcalendar.css.php">
    <link rel="icon" href="/favicon.ico" type="image/ico">
    <link rel="shortcut icon" href="/favicon.ico">
    {%common_getJS('common')%}

<!-- activate xajax (SX) -->
    {$xajax->printJavascript("../../xajax/")}
</head>
<body {$_bodyClass}>
