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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="0; url=../htdocs/de/modules/time/mobile.php">
    <link rel="icon" href="/favicon.ico" type="image/ico">
    <link rel="shortcut icon" href="/favicon.ico">
    <title>openTimetool</title>
</head>
<body>
</body>
</html>
