<!--
    $Id: index.php 2 2006-09-29 07:50:41Z moosach $
    
    Just redirect to htdocs/index.php.

    This first calls the autoprepend "config.php" via the .htaccess file there.
    "config.php" then includes the code of "init.php" which does most of the
    stuff including authentication, session handling, complation, translation 
    and so on. Finally (!) htdocs/index.php is called ...
    
    This is just a copy of the index.php in <openTimetool> = the installation root
    We only changed the redirect to call the mobile log
    
          
-->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
    <title></title>
    <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
    <meta name="description" content="">
    <meta name="abstract" content="">
    <meta name="keywords" content="">
    <meta name="Content-Language" content="en">
    <meta name="robots" content="index, follow">
    <meta name="revisit-after" content="10 days">
    <meta name="author" content="root">
    <meta name="publisher" content="">
    <meta name="copyright" content="">
    <meta name="owner" content="">
    <meta http-equiv="pragma" content="no-cache">
    <meta http-equiv="refresh" content="0; url=../htdocs/de/modules/time/mobile.php"/>
    <link rel="icon" href="/favicon.ico" type="image/ico">
    <link rel="shortcut icon" href="/favicon.ico">
</head>
<body leftmargin="0" topmargin="0" marginheight="0" marginwidth="0">
</body>
</html>