<?php
/**
 * 
 * $Id$
 * 
 * Just redirect to htdocs/index.php.
 * 
 * This first calls the autoprepend "config.php" via the .htaccess file there.
 * "config.php" then includes the code of "init.php" which does most of the
 * stuff including authentication, session handling, complation, translation
 * and so on. Finally (!) htdocs/index.php is called ...
 * 
 * We come here only of course when using a url like http://<server>/openTimetool or
 * http://<server>/openTimetool/index.php but the principle is always the same as
 * already mentioned in init.php and config.php
 * 
 */

$url = 'htdocs/index.php';
$title = 'openTimetool';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="0; url=<?php echo $url; ?>">
    <link rel="icon" href="/favicon.ico" type="image/ico">
    <link rel="shortcut icon" href="/favicon.ico">
    <title><?php echo $title; ?></title>
</head>
<body style="font-size:20px;margin:50px;">
    <a href="<?php echo $url; ?>"><?php echo $title; ?></a>
</body>
</html>
