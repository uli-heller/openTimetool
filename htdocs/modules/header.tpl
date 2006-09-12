<!--
    $Log: header.tpl,v $
    Revision 1.9  2003/03/04 19:18:08  wk
    - do only auto logout when user is logged in

    Revision 1.8  2003/01/13 18:13:00  wk
    - fix the refresh/expire meta

    Revision 1.7  2002/11/25 10:49:42  wk
    - set reload headers properly

    Revision 1.6  2002/10/28 16:24:21  wk
    - include calendar.css

    Revision 1.5  2002/10/24 18:44:19  wk
    - getCssFile doesnt need a parameter

    Revision 1.4  2002/10/24 14:15:07  wk
    - get layout with virtual path now, to be able to use the php file in the layout

    Revision 1.3  2002/10/22 14:43:13  wk
    - include the common js always

    Revision 1.2  2002/07/25 10:11:34  wk
    - use applName as title

    Revision 1.1.1.1  2002/07/22 09:37:37  wk


-->

<!-- include the common macro so we can use common_getJs -->
{%include common/macro/common.mcr%}


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
    <title>{echo strip_tags($config->applName)}</title>
    <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
    <meta name="description" content="Beratung und Konzeption, Datenbanken und Programmierung, Grafik und Design, Internet und Intranet Produktionen">
    <meta name="abstract" content="Funktionalit&auml;t und Design - Multimedia Agentur, Beratung und Konzeption, Datenbanken und Programmierung, Grafik und Design, Internet und Intranet Produktionen">
    <meta name="keywords" content="Beratung, beratung, Design, design, Multimedia, multimedia, Multimediaagentur, multimediaagentur, Internet, internet, Intranet, intranet, Datenbanken, datenbanken, Programmierung, programmierung, PHP, php, Linux, linux, LAMP, lamp, Internet-Anwendungen, Intranet-Anwendungen, Extranet-Anwendungen, Content Management Systeme, CMS, cms, E-commerce, ecommerce, E-Business, ebusiness, Communities, interaktive Kommunikation, digitale Kommunikation, Crossmedia, crossmedia">
    <meta name="Content-Language" content="de">
    <meta name="robots" content="index, follow">
    <meta name="revisit-after" content="10 days">
    <meta name="author" content="vision:produktion GmbH">
    <meta name="publisher" content="vision:produktion GmbH">
    <meta name="copyright" content="vision:produktion GmbH">
    <meta name="owner" content="vision:produktion GmbH">
    <meta http-equiv="pragma" content="no-cache">

<!-- use getOption here!!! is this not implemented??? -->
    {if ($userAuth->isLoggedIn())} <!-- we dont need to reload if the user is not logged in -->
        <meta http-equiv="refresh" content="{$userAuth->options['expire']+5};url={$_SERVER['PHP_SELF']}">

    <link rel="stylesheet" type="text/css" href="{$layout->getCssFile()}">
    <link rel="stylesheet" type="text/css" href="{$config->applPathPrefix}/external/calendar/popcalendar.css.php">
    <link rel="icon" href="/favicon.ico" type="image/ico">
    <link rel="shortcut icon" href="/favicon.ico">
    {%common_getJS('common')%}
</head>
<!-- vision produktion, vision:produktion, vision produktion gmbh, vision produktion GmbH, vision:produktion gmbh, vision:produktion GmbH, visionp, v:p, Vision, vision, Produktion, produktion, Beratung, beratung, Design, design, Multimedia, multimedia, Multimediaagentur, multimediaagentur, Internet, internet, Intranet, intranet, Datenbanken, datenbanken, Programmierung, programmierung, PHP, php, Linux, linux, LAMP, lamp, Internet-Anwendungen, Intranet-Anwendungen, Extranet-Anwendungen, Content Management Systeme, CMS, cms, E-commerce, ecommerce, E-Business, ebusiness, Communities, interaktive Kommunikation, digitale Kommunikation -->
<body leftmargin="0" topmargin="0" marginheight="0" marginwidth="0">
