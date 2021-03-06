<?php
/**
 * 
 * $Id$
 * 
 * Configure your openTimetool here
 * 
 */

/**
 * Try this if openTimetool don't work and you are using a reverse proxy and SSL ...
 */
/*
if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
    $_SERVER['HTTP_HOST'] = $_SERVER['HTTP_X_FORWARDED_HOST'];
}
if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $_SERVER['REMOTE_ADDR'] = $_SERVER['REMOTE_HOST'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
}
if (isset($_SERVER['HTTP_X_FORWARDED_SERVER'])) {
    $_SERVER['SERVER_NAME'] = $_SERVER['HTTP_X_FORWARDED_SERVER'];
}
//$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'];
$_SERVER['HTTPS'] = 'on';
$_SERVER['SERVER_PORT'] = 443;
*/

// AK : Just for documentation. That is builtin php stuff
if (class_exists('stdClass')) {
    $config = new stdClass();
}

/**
 * Don't change ! It's the current version !
 */
$config->applVersion    = '2.4.0';
$config->applName       = 'openTimetool' . $config->applVersion;
$config->schema_version = '2.4.0';

/**
 * AK the link behind the logo on the upper right ...
 * 
 * @var string the logo url
 */
$config->logourl = 'http://sourceforge.net/projects/opentimetool/';

/**
 * AK : maxmimum number of users allowed for this installation
 * 
 * @var integer number of allowed users
 */
$config->numUsers = 999;

/**
 * @var bool enable demo mode
 */
$config->demoMode = false;

/**
 * @var bool allow reset of opcache in system menu
 */
$config->opcache = false;

/**
 * @var bool allow display of phpinfo() in system menu
 */
$config->phpInfo = false;

/**
 * @var string the session name, suffix for "sid4"
 */
$config->sessionName = 'ott';

/**
 * AK : session timeout = auto logout
 * 
 * @var integer number in seconds
 */
// expire after 8 hours -> you need to change session.gc_maxlifetime accordingly
$config->sessionTimeout = 8 * 60 * 60;
// overwriting option from php.ini
ini_set('session.gc_maxlifetime', $config->sessionTimeout);

/**
 * Where to find the includes. Includes are all the external
 * classes and libraries, such as PEAR-packages etc.
 * 
 * @var string the include path
 */
$config->includePath = dirname(__FILE__) . '/includes';

/**
 * Some php related stuff overwriting options from php.ini
 */
// this message comes up because we store our session-vars in the global space :-(
ini_set('session.bug_compat_warn', 0);
// set it to 0 when you have a productive system
ini_set('display_errors', 0);
ini_set('magic_quotes_gpc', 0);
ini_set('max_execution_time', 1000);
/**
 * We need that for php 5.3 to avoid the huge amount of deprecated warnings
 * They will be worked on in future to get rid of them in a good way
 * Some are gone already by the way ...
 */
if (strnatcmp(phpversion(), '5.3.0') >= 0) {
    $php = '5.3'; // or higher
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_STRICT);
} else {
    $php = '5.2'; // or lower
    error_reporting(E_ALL ^ E_NOTICE);
}
$config->php = $php;
ini_set('default_charset', 'utf-8');
// php5.3 needs that in any case
date_default_timezone_set('Europe/Berlin');

/**
 * The DB DSN, as needed for PEAR::DB.
 * 
 * @var string the DB connection parameters
 */
$config->dbDSN = 'mysql://<USERNAME>:<PASSWORD>@localhost/opentimetool';

/**
 * This is the path to html2pdf, this application is needed
 * when you want to export your timesheets to pdf.
 * 
 * @var string the path to html2pdf
 * $config->html2pdf = '/usr/local/bin/html2pdf $1 $2';
 * 
 * There are many tools/scripts around for that purpose.
 * On OpenSuse 10 I'm using that (current adjustment):
 * $config->html2pdf = '/usr/bin/htmldoc --webpage $1 -f $2';
 * 
 * There is a GPL class now in place which does a very rough pdf conversion
 * Just put a comment before the html2pdf line to activate that feature.
 * Needs still some parameter tweaking though
 * AND you'll need
 * '/usr/bin/html2ps'
 * '/usr/bin/ps2pdf'
 * instead that converter below ....
 * It's more likely that you have these 2 ;-) ...
 * Uncomment the following 2 lines if the pathes are different ,,,
 * $config->$html2psPath = '/usr/bin/html2ps';
 * $config->$ps2pdfPath = '/usr/bin/ps2pdf';
 */
$config->html2pdf = '/usr/bin/htmldoc --webpage $1 -f $2';

/**
 * Seperator 4 csv-Export
 */
$config->seperator = ';';

/**
 * Compressed presentation of team members to overcome
 * possible performance problems if there are many projects
 * and users
 * We have 4 compression levels:
 * 0 = uncompressed (like before)
 * 1 = 2 icons with list : managers, members
 * 2 = 1 icon and one list with name suffix [PM] for projectmanagers (icon depends on permissions)
 * 3 = same as 2 with no icon at all; the spartanic mode; even the folder icon is gone ...
 */
$config->teamcompressed = 2;

/**
 * Email header adjustment
 */
//$config->mailAdditionalHeaders    = "From: openTimetool System <noreply@ottsrv.de>\r\n";
//$config->mailAdditionalParameters = "-fnoreply@ottsrv.de";

/**
 * Sorting on "Overview by Project"
 * 0 : as before
 * 1 : sorted by parent, end date and project name
 * 2 : sorted strictly by end date and name
 * Choose which one fits your needs best
 */
$config->project_overview_sort = 0;

/**
 * If there are additional things that need to be included,
 * define the paths here.
 * AK: for instance the path to your PEAR directory
 * 
 * @var string
 */
$config->includePath = $config->includePath . ':/usr/share/php5/PEAR';

/**
 * The various authentication parameters. This application can also
 * use an authentication against different external sources.
 * The following authentication methods are tested
 * 
 * Authentication against openTimetool DB - standard
 */
if (!isset($config->auth)) {
    $config->auth = new stdClass();
}
$config->auth->method  = 'DB';
$config->auth->url     = $config->dbDSN;
$config->auth->digest  = 'md5';
$config->auth->savePwd = true; // password fields shown

/**
 * Authentication against LDAP
 * - mixed LDAP and NON LDAP accounts are possible
 * - user creation on the fly of a properly authenticated LDAP user during first login
 *   (user gets an unknown generated password; after switching to DB auth again he can't log in until
 *   he gets a known password set ! Safety !)
 * - LDAP schema attributes : uid, givenName and sn (the standard attributes in each official schema)
 *   Search by uid = The username in openTimetool
 */
/*
$config->auth->method  = 'LDAP';
$config->auth->url     = 'ldap://<your_ldap.host.com>/<your_basedn>';
$config->auth->digest  = 'md5';
$config->auth->savePwd = false; // password fields hidden
// if you need authentication to ldap server = no anonymous access allowed
$config->auth->ldap_adminid  = ''; // authenticate against LDAP server; see ldap_bind in php manual
$config->auth->ldap_adminpwd = '';
*/

/**
 * actually you can have different run modes, which allow you to configure the
 * application once and run it in different environments, but i removed that for the
 * first openSource version, just to make things easier :-)
 * So dont change this here
 */
$config->runMode = 'live'; // THIS IS THE NORMAL WORKING MODE !
//$config->runMode = 'develop';

// you may overwrite the above values in config by own ones ...
$config_local = dirname(__FILE__) . '/config-local.php';
if (is_file($config_local) && is_readable($config_local)) {
    include_once $config_local;
}

/**
 * Well this code will be run through for any server round trip
 * Authentication, compilation, transalation and all the rest will
 * more or lesse be done there
 */
require_once 'init.php';
