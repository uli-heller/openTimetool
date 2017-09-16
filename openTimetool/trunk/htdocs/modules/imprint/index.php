<?php
/**
 * 
 * $Id$
 * 
 */

require_once '../../../config.php';

$thanks = array(
    array(
        'projectUrl'   => 'http://www.php.net',
        'project'      => 'PHP',
        'comment'      => 'The used scripting language to create the pages and the business logic.',
        'authorUrl'    => 'http://www.php.net/credits.php',
        'copyrightUrl' => 'http://www.php.net/copyright.php',
        'logoUrl'      => 'http://www.php.net/gifs/php_logo.gif',
    ),
    array(
        'projectUrl'   => 'http://pear.php.net',
        'project'      => 'PEAR',
        'comment'      => '',
        'authorUrl'    => 'http://pear.php.net/credits.php',
        'copyrightUrl' => 'http://www.php.net/copyright.php',
        'logoUrl'      => 'http://pear.php.net/gifs/pear.gif',
    ),
    array(
        'projectUrl'   => 'http://www.mysql.com',
        'project'      => 'mySQL',
        'comment'      => 'The DB saving all the data.',
        //'authorUrl'    => '',
        'copyrightUrl' => '',
        'logoUrl'      => 'http://www.mysql.com/images/interface-logo.png',
    ),
    array(
        'projectUrl'   => 'http://www.geocities.com/fuushikaden/PopupCalendar/index.htm',
        'project'      => 'Date Picker',
        'comment'      => 'A really cool JS date picker script.',
        'author'       => 'Fuushikaden',
        'authorUrl'    => 'http://www.geocities.com/fuushikaden/PopupCalendar/dp-credits.htm',
        'copyrightUrl' => '',
        'logoUrl'      => '',
    ),
);

require_once $config->finalizePage;
