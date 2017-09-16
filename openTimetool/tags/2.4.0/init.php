<?php
/**
 * 
 * $Id$
 * 
 * Beside config.php the central code which will be run through with each server
 * round trip !
 * 
 * Changes by Anton Kejr :
 *  - Some minor clean up 2006/08/26
 *  - some env-settings
 *  - Benchmark/Timer.php : use the actual PEAR package now !
 * 
 */

// for debugging only
//ini_set('display_errors', 'On');

include_once 'db_upgrade.php';

/**
 * The include path; see benchmark-stuff below ...
 */
ini_set('include_path', $config->includePath . ':' . ini_get('include_path'));

/**
 * Messure the time needed to run through all authentication, compilation ...
 * 
 * Note : if you are using runmode = 'develop'in config.php, this code is called
 * and latest then you'll need the PEAR package on your system and the path to PEAR
 * in your include path ! But see README_PEAR ...
 */
if (strpos($config->runMode, 'develop') === 0) {
    require_once 'Benchmark/Timer.php';
    $processingTimer = new Benchmark_Timer;
    $processingTimer->start();
}

if ($config->demoMode) {
    $config->opcache = false;
    $config->phpInfo = false;
}

/**
 * Some general XINE template initialisation stuff.
 * On other words : any application using XINE needs that initially
 * 
 * class vp_Application_Config extends HTML_Template_Xipe_Options
 * 
 * For instance the application pathes (applroot, tmp-dir, ...) are set here
 * Also included are language setting and error handling
 * 
 */
require_once 'vp/Application/Config.php';
$config = new vp_Application_Config($config, dirname(__FILE__), true);

// AK : the only use I found up to now is '$config->isLiveMode()' which simply
//      returns true if the runmode set in config.php is in that array !?!?
$config->setOption('liveModes', array(
    'live', 'develop-live', 'live at home', 'anton.kejr@system-worx.de',
));

$config->availableLanguages = array(
    'en' => array('language' => 'english', 'flag' => 'uk'),
    'de' => array('language' => 'deutsch', 'flag' => 'germany'),
    //'es' => array('language' => 'espa&ntilde;ol', 'flag' =>'spain'),
    //'fr' => array('language' => 'french', 'flag' =>'france'),
);

$config->exportDir      = $config->applRoot . '/tmp/_exportDir';
$config->OOoTemplateDir = $config->applRoot . '/tmp/_OOoTemplateDir';
$config->cacheDir       = $config->tmpDir . '/_cache';

// will die if failure !
$config->check_installation();

//$config->setFeature('translate', 'develop');
$config->setFeature('translate', true);
$config->setLanguages(array('en', 'de'));

// AK : This is something for the next version ! Actually inclomplete stuff !
$config->setFeature('price', false);

/**
 * init the session object here, because we need the session data
 */
$sessionname = 'sid4' . preg_replace('/<.*>|[^a-z0-9]/i', '', $config->sessionName);
session_name($sessionname);
//if (!empty($_REQUEST[$sessionname])) {
//    session_id($_REQUEST[$sessionname]);
//}

if (!$config->isLiveMode()) {
    include_once $config->applRoot . '/logging.php';
    $logging->_logme('init', 'before session_start');
    //$logging->_logme('init Session: ', print_r($_SESSION, true));
    //$logging->_logme('init sname: ', print_r($sessionname, true));
    //$logging->_logme('init GetSID: ', print_r($_REQUEST[$sessionname], true));
    //$logging->_logme('init current page: ', print_r($_SERVER['PHP_SELF'], true));
    //$logging->_logme('init REQUEST: ', print_r($_REQUEST, true));
}

$res = session_start();

// this object is to store individual session variables in an extra namespace
// NOTE : these register functions are deprecated in the meanwhile
// we soon change them to simple isset($_SESSION['session']) ...
//if (!session_is_registered('session')) {
if (!isset($_SESSION['session'])) {
    // standard PHP-class constructor
    $session = new stdClass();
    //session_register('session');
    $_SESSION['session'] = '';
    if (!empty($_REQUEST['template']) && $_REQUEST['template'] == 'yes') {
        // check to see if we come from fopen and export and try to read the session data from file ...
        // see htdocs/moduls/OOExport.php
        // we overcome with this the php problem that at one point in time the session has been
        // created new and empty when called by fopen...
        $tmpsessfile = $config->applRoot . '/tmp/OOExport/' . $_REQUEST[$sessionname];
        if ($fp = fopen($tmpsessfile, 'rb')) {
            $sessdata = fread($fp, filesize($tmpsessfile));
            fclose($fp);
            session_decode($sessdata);
            unlink($tmpsessfile);
            $session = &$_SESSION['session'];
        }
    }
} else {
    // since the class is read from the session it is not automatically made global
    $session = &$_SESSION['session'];
}

if (!$config->isLiveMode()) {
    $logging->_logme('init', 'after session_start');
    //$logging->_logme('init Session: ', print_r($_SESSION, true));
    //$logging->_logme('init (new)SID: ', print_r(session_id(), true));
}

$lang = $_SESSION['lang'];
$config->langHandler($lang);
// save the current language in the session
$_SESSION['lang'] = $lang;

/**
 * instanciate the applError and applMessage here, w/o DB
 * later, when we have a db-connection we tell it to write to the DB
 * but since $account may set errors we need the applError here
 */
$options = array(
    'columns' => array(
        'session_id' => 'session_id()',
        'user_id'    =>
            '($GLOBALS["userAuth"] && !Auth::isError($GLOBALS["userAuth"]) &&' .
            ' $GLOBALS["userAuth"]->isLoggedIn())?$GLOBALS["userAuth"]->getData("id"):0',
    ),
);

if (!$config->isLiveMode()) {
    $options['verbose'] = true;
}
require_once 'vp/Page/Error.php';
require_once 'vp/Page/Message.php';
$applError = new vp_Page_Error(null, $options);
$applMessage = new vp_Page_Message();

/**
 * prepare the account, the constructor does that automatically
 */
require_once $config->classPath . '/modules/account/account.php';

if ($account->isAspVersion()) {
    // if the version of is the asp-version
    if (isset($session->account->ttVersion) &&
            $session->account->ttVersion != $config->applVersion) {
        $_oldVersion = $config->applVersion;
        $config->applPathPrefix = str_replace(
            $config->applVersion,
            $session->account->ttVersion,
            $config->applPathPrefix
        );
        $config->applVersion = $session->account->ttVersion;
        // redirect to the new url and dont send the session id with it!
        HTTP_Header::redirect(str_replace(
            "/$_oldVersion/",
            "/{$config->applVersion}/",
            $_SERVER['PHP_SELF']
        ), false);
    }

    $config->tablePrefix = @$session->account->data['tablePrefix']
                         ? @$session->account->data['tablePrefix'] : '';
    $config->dbDSN .= @$session->account->data['dbName']
                    ? @$session->account->data['dbName'] : '_fallback';
}

/**
 * DB-table defines
 */
define('TABLE_USER' ,            $config->tablePrefix . 'user' );
define('TABLE_CUSTOMER',         $config->tablePrefix . 'customer');
define('TABLE_PROJECT',          $config->tablePrefix . 'project');
define('TABLE_TIME',             $config->tablePrefix . 'time');
define('TABLE_TASK',             $config->tablePrefix . 'task');
define('TABLE_ERRORLOG',         $config->tablePrefix . 'errorLog');
define('TABLE_PROJECTTREE',      $config->tablePrefix . 'projectTree');
define('TABLE_PRICE',            $config->tablePrefix . 'price');
define('TABLE_PROJECTTREE2USER', $config->tablePrefix . 'projectTree2user');

define('TABLE_EXPORTED2PROJECT', $config->tablePrefix . 'exported2project');
define('TABLE_EXPORTED',         $config->tablePrefix . 'exported');
define('TABLE_OOOTEMPLATE',      $config->tablePrefix . 'OOoTemplate');

// this table exists only once, especially in the ASP version, which uses only one DB
define('TABLE_TRANSLATE_',       'translate_');

/**
 * includes
 */
require_once 'DB.php';
require_once 'I18N/DateTime.php';

// put those in the include path, not as they are now in the appl-root
require_once 'vp/Page/Layout.php';
require_once 'vp/Application/HTML/PageProperty.php';

require_once 'vp/Util/HTML.php';

require_once $config->classPath . '/util.php';
require_once $config->classPath . '/modules/user/user.php';

/**
 * DB connect
 */
if (isset($config->dbDSN)) {
    if (DB::isError($db = DB::connect($config->dbDSN,
            array('debug' => true, 'persistent' => true)))) {
        print 'DB::connect failed!!!<br>';
        if (!$config->isLiveMode()) {
            print_r($db);
        }
    } else {
        $db->setFetchMode(DB_FETCHMODE_ASSOC);
    }
}

/**
 * init template engine
 */
if (!$config->isLiveMode()) {
    $processingTimer->setMarker('init.php before tpl-instanciating');
}

require_once 'HTML/Template/Xipe.php';
$options = array(
    'templateDir'  => $config->applRoot,
    'compileDir'   => 'tmp', // use the compile dir 'tmp' under the template dir
    'verbose'      => true,  // this is default too
    'logLevel'     => 0,     // 0 = dont write log files
    'filterLevel'  => 10,    // apply all the most common filters
    'enable-Cache' => true,
    'locale'       => 'en',
);
$tpl = new HTML_Template_Xipe($options);

if (!$config->isLiveMode()) {
    $processingTimer->setMarker('init.php after tpl-instanciating');
}

// AK We can set here various debugging switches when running in develop-mode (->config.php)
// only on my machine i want this, the other shall see cached files
if ($config->runMode == 'develop') {
    $tpl->setOption('logLevel', 2);
    $tpl->setOption('forceCompile', true);
    $tpl->setOption('filterLevel', 10);
}

require_once 'HTML/Template/Xipe/Filter/Modifier.php';
$modifiers = new HTML_Template_Xipe_Filter_Modifier($tpl->options);
$tpl->registerPrefilter(
    array(&$modifiers, 'imgSrc'),
    array($config->mediaRoot, $config->vMediaRoot)
);

require_once 'I18N/Messages/Translate.php';
require_once 'HTML/Template/Xipe/Filter/Translate.php';

if (!$config->isLiveMode()) {
    $processingTimer->setMarker('init.php tpl before detecting lang');
}

$translateOptions = array(
    'tablePrefix'    => TABLE_TRANSLATE_,
    'sourceLanguage' => 'en',
    // we are translating from english, so we can afford to be
    // lazy and not watch the cases
    'caseSensitive'  => false,
);
$translator = new I18N_Messages_Translate($db, $translateOptions);

if (!$config->isLiveMode()) {
    $processingTimer->setMarker('init.php tpl before lang-stuff');
}

if ($lang) {
    $tpl->setOption('locale', $lang);
    $tpl->registerPostfilter(array(&$translator, 'translateMarkUpString'), $lang);

    // this filter translates PHP-generated text
    // it simply does out of < ? =$text ? >  this < ? =translateAndPrint($text) ? >
    // but only within the $translator->possibleMarkUpDelimiters, so not every
    // < ?= is translated !!! since that is not wanted anyway,
    // i.e. think of "<td colspan={$colspan}>" - doesnt need translation

    $translateFilter = new HTML_Template_Xipe_Filter_Translate($tpl->getOptions());
/*
    $tpl->registerPostfilter(
        array(&$translateFilter, 'applyTranslateFunction'),
        array('$GLOBALS[\'util\']->translateAndPrint', $translator->possibleMarkUpDelimiters)
    );
*/
    $tpl->registerPostfilter(
        array(&$translateFilter, 'translateMarkedOnly'),
        array('$GLOBALS[\'util\']->translateAndPrint')
    );
}

if (!$config->isLiveMode()) {
    $processingTimer->setMarker('init.php tpl block done');
}

/*
$headers = getallheaders();
if ($headers['Authorization']) {
    // XMLRPC sends this auth !!!
    if ($auth = trim(str_replace('Basic', '', $headers['Authorization']))) {
        $decoded = base64_decode(trim($auth));
        $authData = explode(':', $decoded);
    }
    // log the user in, using the received data
    $this->login($authData[0], $authData[1]);
}
*/

/**
 * user authentication
 */
require_once 'Auth/Auth.php';
$options = array(
    'expire'        => $config->sessionTimeout, // expire after 8 hours (AK)
    'protectRoot'   => $config->applPathPrefix . '/modules',
    //'protect'       => array('/city/myrooms.php','/city/editRoom.php','imail'),
    'dontProtect'   => array('*.css.*', '/imprint', '/manual', '/remote'),
    'loginPage'     => 'user/login.php',
    'errorPage'     => 'user/login.php?loginFailed=true', //AK : deleted leading /
    'defaultPage'   => 'user/login.php', //AK : deleted leading /
    'digest'        => $config->auth->digest,
    // ignore the protectRoot for the hash, because
    // it might changes, once the language changes
    'ignoreForHash' => array('protectRoot'),
);

if (!$config->isLiveMode()) {
    $options['logFile'] = $config->tmpDir . '/Auth.log';
    //include_once $config->applRoot . '/logging.php';
    //$logging->_logme('init', 'log file set');
}
if ($config->auth->method == 'DB') {
    $options['table']          = TABLE_USER;
    $options['usernameColumn'] = 'login';
    $options['passwordColumn'] = 'password';
}
//$userAuth = Auth::setup('NIS', 'vpnis', $options);
$userAuth = Auth::setup($config->auth->method, $config->auth->url, $options);
if (Auth::isError($userAuth)) {
    // this doesnt make sense, but :-)
    $applError->set('Authentication modules could not be loaded properly, please contact your vendor!');
    if (!$config->isLiveMode()) {
        print_r($userAuth);
    }
} else {
    // if the account switched, logout the user and let him/her log in again
    if ($account->accountChanged) {
        $userAuth->logout();
        header('Location: ' . $_SERVER['PHP_SELF']);
        die();
    }

    if ($userAuth->isLoggedIn() && !$userAuth->getData('id')) {
        // set the user data in the auth-data so we can use them from there easily
        // since we might authenticate against another source,
        // such as imap where we dont get those data from!
        $user->setWhere('login=' . $db->quote($userAuth->getUsername()));
        $users = $user->getAll();
        // reset the where clause, so the user can be used normally again
        $user->setWhere();
        if (sizeof($users) == 1) {
            $userAuth->setData($users[0]);
            // force to get all the account-data!, since they might have changed,
            // this has no effect on a NET version
            $account->prepare(true);
            if (!$account->isActive()) {
                $userAuth->logout();
                header('Location: ' . $config->home);
                die();
            }
        }
    }
}

if ($account->isAspVersion() && $account->getAccountName() && !$account->isActive()) {
    $applError->set('Your account is not active, please contact your vendor!');
    if ($userAuth->isLoggedIn()) {
        $userAuth->logout();
        header('Location: ' . $config->home);
        die();
    }
}

if (!$config->isLiveMode()) {
    //include_once $config->applRoot . '/logging.php';
    //$logging->_logme('init', 'Autenticated : ' . print_r($userAuth, true));
    //$logging->_logme('init', 'Autenticated');
}

/*
    // this class includes a lot of others, so we need to include it after all the other classes
    // are instanciated, like userAuth, etc. FIXXME some day
    require_once $config->classPath . '/modules/user/user.php';
*/

/**
 * predefine some commonly used variables
 */
// the TABLE_ERRORLOG was not defined when we instanciated the object
$applError->setOption('table', TABLE_ERRORLOG);
//FIXXXXXME this is not very nice, check someway else if a db is selected
if (!DB::isError($db) && $db->dsn['database'] &&
        ($account->isAspVersion() && $account->getAccountName())) {
    $applError->setDbConnection($db);
}

// register the objects, so we can use the error/message methods in the config-class
$config->registerErrorObject($applError);
$config->registerMessageObject($applMessage);

/**
 * Automatic db upgrade interface (starting with 2.3.0)
 */
upgrade_database();

$dateTime = new I18N_DateTime($lang);
$utilHtml = new vp_Util_HTML;

// this could also be done by overriding the concerning methods ...
// i dont know what is better, i pass options now :-)
$options = array(
    'layoutPath'         => $config->applRoot . '/layout',
    'virtualLayoutPath'  => $config->applPathPrefix . '/layout',
    // $config->applRoot would be the same, but this is 'more' secure,
    // since we need the tempalte dir :-)
    'templateDir'        => $tpl->options['templateDir'],
    'virtualTemplateDir' => $config->applPathPrefix,
);
$layout = new vp_Page_Layout($options);

// be sure to have set 'main' as default,
// if someone wnats something else it needs to be done explicitly
// by setting this here, we reset the 'dialog' in case it was set, is necessary!
//$layout->setMainLayout('main');
//$layout->setCurrentTemplate(''); // this is wrongly saved,
// FIXXXME actually we are only saving $layout in the session for the layout, so think it over again
// FIXXXME do this via a destructor, unset it after processing the page is done!
//$appTimer = new Benchmark_Timer;
//$appTimer->setMarker('end of config.php');

// what kind of data do exist in the session:
// $session->temp
// $session->accountData     read from the timetool_admin application

// AK: Various php notices eliminated during initial start
if (isset($_REQUEST['setLayout'])) {
    $session->layout = strip_tags($_REQUEST['setLayout']);
}
if (!isset($session->layout) && $config->runMode != 'live at v:p') {
    // well that live at v:p makes the window red; we'll never use that ;-)
    $session->layout = 'demo';
}
if (isset($session->layout)) {  
    $layout->setLayout($session->layout);
}

if (!$config->isLiveMode()) {
    $processingTimer->setMarker('init.php done');
}

$properties = array(
    'modules/time/summary'   => array('pageHeader'=>'Summary'),
    'modules/time/multi'     => array('pageHeader'=>'Multi-Log','manualChapter'=>'log_multiLog'),
    'modules/time/quick'     => array('pageHeader'=>'Quick-Log','manualChapter'=>'log_quickLog'),
    'modules/time/mobile'    => array('pageHeader'=>'Mobile-Log','manualChapter'=>'log_mobileLog'),                
    'modules/time/holiday'   => array('pageHeader'=>'Period-Log','manualChapter'=>'log_periodLog'),
    'modules/time/today'     => array('pageHeader'=>'Today-Log','manualChapter'=>'log_todayLog'),
    'modules/time/index'     => array('pageHeader'=>'time overview and filter','manualChapter'=>'analyze'),
    'modules/time/week'      => array('pageHeader'=>'overview by week','manualChapter'=>'analyze_week'),
    'modules/time/project'   => array('pageHeader'=>'overview by project','manualChapter'=>'analyze_project'),
    'modules/export/OOo'     => array('pageHeader'=>'Export - OpenOffice.org *','manualChapter'=>'export_OOo'),
    'modules/export'         => array('pageHeader'=>'Export','manualChapter'=>'export'),

    'modules/project/member' => array('pageHeader'=>'project - team members','manualChapter'=>'project_team'),
    'modules/project'        => array('pageHeader'=>'projects','manualChapter'=>'project'),
    'modules/task'           => array('pageHeader'=>'tasks','manualChapter'=>'task'),
    'modules/price'          => array('pageHeader'=>'prices'),

    'modules/user/adminMode' => array('pageHeader'=>'Admin mode'),
    'modules/user/login'     => array('pageHeader'=>'Login'),
    'modules/user/password'  => array('pageHeader'=>'Change Password'),
    'modules/user'           => array('pageHeader'=>'users'),

    'modules/imprint/index'  => array('pageHeader'=>'Thanks'),
);

$pageProp = new vp_Application_HTML_PageProperty($properties, $config);

/**
 * styles
 */
// this is the default style, overwrite what u want
$styleSheet = new stdClass();
$styleSheet->mainColor         = '#B70F18';
$styleSheet->fontColor         = 'black';
$styleSheet->invertedFontColor = 'white';
$styleSheet->lighterColor      = '#DEDEDE';
$styleSheet->bgHighlightColor  = '#EEEEEE';
$styleSheet->bgHighlightColor1 = '#FAF8F8';
$styleSheet->fontSize          = '12px';
$styleSheet->darkerColor       = '#C1C1C1';
$styleSheet->alertColor        = '#FF0000';
$styleSheet->vpRedDotColor     = '#FB191D';
//$styleSheet->backgroundColor   = '';

switch ($session->layout) {
    case 'demo':
        $styleSheet->bgHighlightColor1 = '#EEEEFE';
        $styleSheet->mainColor         = '#666699';
        break;
}

function t($string)
{
    global $util;
    return $util->translate($string);
}

include_once 'messages.php';
