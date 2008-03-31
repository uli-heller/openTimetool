<?php
	/**
	*
	*  $Id
	*
	* Anton Kejr (AK) :	This beast is called anytime by openTimetool/htdocs/.htaccess
	*						via auto_prepend. Always keep that in mind !!
	*
	*/

    // AK : Just for documentation. That is builtin php stuff 
    if (!class_exists("stdClass")) $config = new stdClass;


    /**
	 * Don't change ! It's the current version !
	 */
    $config->applVersion = '2.2.0.3';
    $config->applName = 'openTimetool'.$config->applVersion;
    
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
    * AK : session timeout = auto logout
    * 
    * @var integer number in seconds
    */
    $config->sessionTimeout = 8*60*60;  // expire after 8 hours -> you need to change session.gc_maxlifetime accordingly
    ini_set('session.gc_maxlifetime',$config->sessionTimeout);  // overwriting option from php.ini
  
    /**
    * Where to find the includes. Includes are all the external 
    * classes and libraries, such as PEAR-packages etc.
    * 
	* @var string the include path 
    */
    $config->includePath = dirname(__FILE__).'/includes';
   
   
   	/**
   	 * Some php related stuff overwriting options from php.ini
   	 */
    // this message comes up because we store our session-vars in the global space :-(
    ini_set('session.bug_compat_warn',0);
    // set it to 0 when you have a productive system
    ini_set('display_errors',1);
    ini_set('magic_quotes_gpc',0);
    ini_set('max_execution_time',1000);
 

    /**
    * The DB DSN, as needed for PEAR::DB.
    *
    * @var string the DB connection parameters
    */
    $config->dbDSN              = 'mysql://<account>:<password>@localhost/openTimetool';

    /**
    * This is the path to html2pdf, this application is needed
    * when you want to export your timesheets to pdf.
    * 
    * @var string the path to html2pdf
    * 
    * There are many tools/scripts around for that purpose.
    * On OpenSuse 10 I'm currently using that :
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
    $config->html2pdf = '/usr/local/bin/html2pdf $1 $2';


    /**
    * Automatically determined!    AK !!!!!WORKS only yet when not defined. Have to look on ...
    * This is the prefix of the path that leads to this application via a user agent.
    * I.e. if the application is reachable under this domain:
    *   http://www.myhost.com/myApps/timetool
    * then this variable would need to be '/myApps/timetool'
    * If the application runs directly on a virtual host, this string would need
    * to be empty ''
    * 
    * @var string the application path prefix
    */
//    $config->applPathPrefix     = '/openTimetool';       
    /**
    * Automatically determined!		AK !!!!! WORKS only when not in yet 
    *
    */
//    $config->applRoot           = $_SERVER['DOCUMENT_ROOT'].$config->applPathPrefix.'/htdocs';  
//    $config->finalizePage       = $_SERVER['DOCUMENT_ROOT'].$config->applPathPrefix.'/htdocs/finalize.php';



    /**
    * If there are additional things that need to be included, 
    * define the paths here. 
    * AK: for instance the path to your PEAR directory
    * 
    * @var string 
    */
    $config->includePath        = $config->includePath.":/usr/share/php5/PEAR";

    /**
    * The various authentication parameters. This application can also
    * use an authentication against an external source, but this isn't tested
    * yet. So we don't include this information by now
    * Please stay with this one for now :
    * 
    */
    $config->auth->method       =   'DB';
    $config->auth->url          =   $config->dbDSN;
    $config->auth->digest       =   'md5';
    $config->auth->savePwd      =   true;    

    /** 
    * actually you can have different run modes, which allow you to configure the 
    * application once and run it in different environments, but i removed that for the
    * first openSource version, just to make things easier :-)
    * So dont change this here
    */
    $config->runMode = 'live';		// THIS IS THE NORMAL WORKING MODE !
    //$config->runMode = 'develop';
	
	

	// you may overwrite the above values in config by own ones ...
    $config_local = dirname(__FILE__).'/config-local.php';
    if (is_file($config_local)) {
        include_once $config_local;
    }

	/**
	 * Well this code will be run through for any server round trip 
	 * Authentication, compilation, transalation and all the rest will 
	 * more or lesse be done there  
	 */
    require_once 'init.php';

?>