<?php
/**
 * 
 * $Id
 * 
 * Some general XINE template initialisation stuff.
 * On other words : any application using XINE needs that initially
 * 
 * class vp_Application_Config extends HTML_Template_Xipe_Options
 *  
 * For instance the application pathes (docroot, tmp-dir, ...) are set here
 * Also included are language setting and error handling 
 * 
 * 
 * ****************** Switch to SVN *************
*  $Log: Config.php,v $
*  Revision 1.3  2003/03/11 12:57:55  wk
*  *** empty log message ***
*
*  Revision 1.19  2003/03/04 19:04:23  wk
*  - added tmpDir
*
*  Revision 1.18  2003/01/29 16:23:50  wk
*  - made it work properly
*
*  Revision 1.17  2003/01/29 09:58:52  wk
*  - resolve some E_ALL issues
*  - add langHandler
*
*  Revision 1.16  2003/01/27 18:13:38  wk
*  - handle https
*  - unremove removed code
*
*  Revision 1.15  2003/01/27 10:21:18  wk
*  - take care of presetted values
*
*  Revision 1.14  2003/01/17 18:47:32  wk
*  - use xipe now!
*
*  Revision 1.13  2003/01/07 15:20:30  wk
*  - removed call to a method with a reference
*
*  Revision 1.12  2002/12/01 12:21:27  wk
*  - added htdocsDir
*
*  Revision 1.11  2002/11/29 19:38:09  wk
*  - set tablePrefix to empty if not given
*
*  Revision 1.10  2002/11/27 12:03:44  wk
*  - fix the suffix stuff
*
*  Revision 1.9  2002/11/27 11:55:06  wk
*  - added the user-suffix stuff
*
*  Revision 1.8  2002/11/27 10:52:32  wk
*  - added some methods that can also be called statically
*
*  Revision 1.7  2002/11/27 10:26:59  cb
*  - properly set DOC_ROOT also for php >4.2.x
*
*  Revision 1.6  2002/11/22 19:32:57  wk
*  - added secureMode
*
*  Revision 1.5  2002/11/19 19:54:19  wk
*  - translate messages and errors
*
*  Revision 1.4  2002/11/12 13:24:02  wk
*  - added some doc
*
*  Revision 1.3  2002/11/11 17:51:30  wk
*  - made it work for using with Error.mcr
*
*  Revision 1.2  2002/06/26 16:16:30  wk
*  - implementation of the first methods
*
*  Revision 1.1  2002/06/19 15:24:58  wk
*  - first checkin for vp
*
*/

require_once 'HTML/Template/Xipe/Options.php';

/**
*   this class handles common stuff that is needed for the
*   configuration of an application
*
*   @package  vp/Application
*   @access   public
*   @author   Wolfram Kriesing <wolfram@kriesing.de>
*
*/
class vp_Application_Config extends HTML_Template_Xipe_Options
{
    /**
    *   @var    array   $options    you need to overwrite this array and give the keys, that are allowed
    */
    var $options = array(   'applName'          =>  ''
                            ,'dbDsn'            =>  ''

                            //'serverRoot'        =>  $S_SERVER['DOCUMENT_ROOT'],
                            ,'applRoot'         =>  ''
                            ,'tempRoot'         =>  ''

                            //'virtualServerRoot' =>  "http://".$S_SERVER["HTTP_HOST"],
                            ,'virtualApplRoot'  =>  ''
                            //'virtualTempRoot'   =>  ''  //
                            ,'liveModes'        =>  array('live')
                        );

    var $_features = array();
    var $_languages = array();    
                                 
    /**
    *   set this to true if a dev-env is used, which is user-specific, like we have it on andrea
    *   this means the username is extracted from the path and the db-name, etc. are modified accordingly
    */
    var $userSpecificEnv = false; 
    
    var $userSuffix = '';

    /**
    *   has to be set by the application
    */
    var $runMode = '';

    /**
    *   __constructor
    *   @param
    *   @param  string  the root directory of the application
    */
    function vp_Application_Config( $initObject , $rootDir , $secureMode=false )
    {
        // init this object with the properties of $initObject
        $allProps = get_object_vars($initObject);
        foreach( $allProps as $key=>$val )
            $this->$key = $val;
        
        // we always want to resolve symbolic links, etc. we realized that on timetool.biz
        $rootDir = realpath($rootDir);
        $this->init( $rootDir , $secureMode );
    }

    function init( $rootDir , $secureMode=false )
    {
        // secure mode is simply the new structure
        if ($secureMode) {
            if (!isset($this->htdocsDir)) {
                $this->htdocsDir = 'htdocs';
            }
        }

        $_SERVER['DOCUMENT_ROOT'] = $this->getDocRoot();
        if (!isset($this->applPathPrefix)) {
            $this->applPathPrefix = $this->getApplPathPrefix($rootDir,$secureMode);
        }

        if (!isset($this->serverRoot)) {
            $this->serverRoot     =   realpath($_SERVER['DOCUMENT_ROOT']);
        }
        if (!isset($this->vServerRoot)) {
            $this->vServerRoot    =   'http'.(@$_SERVER['HTTPS']=='on'?'s':'').'://'.$_SERVER['HTTP_HOST'];
        }
        if (!isset($this->applRoot)) {
            $this->applRoot       =   $this->serverRoot.$this->applPathPrefix;
        }
        $this->vApplRoot      =   $this->vServerRoot.$this->applPathPrefix;

        // virtual paths
        $this->home       =   $this->applPathPrefix.'/index.php';
        $this->mediaRoot  =   $this->applRoot.'/media';
        $this->vMediaRoot =   $this->applPathPrefix.'/media';
        $this->vImgRoot   =   $this->applPathPrefix.'/media/image';
        
        $this->tmpDir     =   $this->applRoot.'/tmp';
        
        // local paths
        $this->classPath  =   ($secureMode?$rootDir:$this->applRoot).'/classes';
        if (!isset($this->finalizePage)) {
            $this->finalizePage = $this->applRoot.'/finalize.php';
        }


        // if the table prefix is not given we set it to empty by default
        if (!isset($this->tablePrefix)) {
            $this->tablePrefix = '';
        }

        if ($this->userSpecificEnv) {
            $this->dbDSN .= $this->getUserSuffix();
        }

    }

    /**
    *   this method can also be used statically!!!
    *
    */
    function getDocRoot()
    {
    	  // AK : correct php notice -> $GLOBALS[..] only available when register_globals=on (php.ini) 
    	  if(isset($GLOBALS['DOCUMENT_ROOT']))
    	  {
	        if( $GLOBALS['DOCUMENT_ROOT'] && !$_SERVER['DOCUMENT_ROOT'] )
   	         $_SERVER['DOCUMENT_ROOT'] = $GLOBALS['DOCUMENT_ROOT'];
		  }
			
        // oh boy... if the DocumentRoot in the webserver ends with a slash we dont have to add the slash
        // but if it doesnt end with a slash we need to add the slash, as we do here by default
        if( substr($_SERVER['DOCUMENT_ROOT'],-1) == '/' )
            $_SERVER['DOCUMENT_ROOT'] = substr($_SERVER['DOCUMENT_ROOT'],0,-1);

        return $_SERVER['DOCUMENT_ROOT'];
    }

    /**
    *   this method can also be used statically!!!
    *
    */
    function getApplPathPrefix( $rootDir )
    {
        $_SERVER['DOCUMENT_ROOT'] = vp_Application_Config::getDocRoot();

        // this is the path-prefix to the application,
        // the part of the path which normally needs to be added behind the DOCUMENT_ROOT
        $applPathPrefix = str_replace(  $_SERVER['DOCUMENT_ROOT'] , '' ,
                                        $rootDir.   ($this->htdocsDir?'/'.$this->htdocsDir:'')  );
//        $this->applPathPrefix = str_replace( $_SERVER['DOCUMENT_ROOT'] , '' , dirname(__FILE__) );
        return $applPathPrefix;
    }
                           
    /**
    *   this extracts the username from the path, like
    *   out of http://andrea/wolfram/www.timetool.biz - 'wolfram' is extracted
    *   
    *   @return string  the user suffix
    */
    function getUserSuffix()
    {
        $this->userSuffix = preg_replace('/(^\/?)([^\/]*).*/','$2', $this->applPathPrefix );
        return $this->userSuffix;
    }

    /**
    *   set a feature's state
    *   as value u can either give true or false or the runMode
    *
    *   @author Wolfram Kriesing <wk@visionp.de>
    *   @param  string      the feature name
    *   @param  mixed       either boolean or a string
    *                       boolean to turn the feature on or off
    *                       string the runMode name for which it shall be turned on
    */
    function setFeature( $name , $value )
    {
        $this->_features[$name] = $value;
    }

    /**
    *   check if a feature is turned on
    *   a feature is turned on either when it is true or when
    *   the current runMode is saved in the feature's value
    *
    *   @author Wolfram Kriesing <wk@visionp.de>
    *   @param  string      the feature name
    *   @return boolean     true if the feature is turned on
    */
    function hasFeature( $name )
    {
        if( isset($this->_features[$name]) && ($this->_features[$name] === true || $this->_features[$name] == $this->runMode) ) {
            return true;
        }
        return false;
    }

    /**
    *   set the available languages
    *
    *   @author Wolfram Kriesing <wk@visionp.de>
    *   @param  array   an array of the available language codes
    *                   the first one is the fallback language !!!
    */
    function setLanguages( $values )
    {
        $this->_languages = $values;
    }         

    function getLanguages()
    {
        return $this->_languages;
    }

    /**
    *   check if the given language is available yet
    *   if not returns the fallback language
    *
    *   @author Wolfram Kriesing <wk@visionp.de>
    *   @param  string  the language code to check for
    *   @return string  the available language code
    */
    function checkLanguage( $lang )
    {
        if( in_array($lang,$this->_languages) )
            return $lang;
        if( $this->_languages[0] )                  // check if a fallback language is given
            return $this->_languages[0];
        return false;
    }

/**
 * Language handler : needed for setting and switching of the language which
 * changes the directory :  /en/ to /de/ for instance
 */

    function langHandler(&$lang)
    {
    
         $oldLang = $lang;
        // if the languages has changed (the directory was changed, i.e. from /en/ to /de/)
        $preLen = strlen($this->applPathPrefix);
        $newLang = substr(str_replace('//','/',$_SERVER['PHP_SELF']),$preLen,4);
        $_setLanguage = null;
        if ($newLang[0]=='/' && $newLang[3]=='/') {
            foreach( $this->_languages as $aLang ) {
                if ($newLang == "/$aLang/") {
                    $lang = $aLang;
                    break;
                }
            }
        }

        if( !$lang ) {
            require_once( 'I18N/Negotiator.php' );
            $neg = new I18N_Negotiator;
            $lang = $this->checkLanguage($neg->getLanguageMatch());
        }

        $oldApplPathPrefix = $this->applPathPrefix;
        $this->applPathPrefix = $this->applPathPrefix."/$lang";
        if( $newLang != "/$lang/" ) {
            //$newLang = preg_replace('~^/('.implode('|',$_allLangs).')~','',$_SERVER['PHP_SELF']);


            $phpSelf = str_replace($oldApplPathPrefix,'',$_SERVER['PHP_SELF']);
            //($oldLang?"/$oldLang":'')
            require_once 'HTTP/Header.php';
            HTTP_Header::redirect($this->applPathPrefix.$phpSelf);
        }
    }

    /**
    *
    *
    *   @author Wolfram Kriesing <wk@visionp.de>
    *   @version    11/11/2002
    *   @param  string  the language code to check for
    *   @return string  the available language code
    */
    function isLiveMode()
    {
        if( in_array( $this->runMode , $this->getOption('liveModes') ) )
            return true;
        return false;
    }             
                   
    /**
    *
    *   all methods for error and message handling, checking etc.
    *
    */

    function registerErrorObject( &$errorObj )
    {
        $this->_errorObj = &$errorObj;
    }

    function &getErrorObject()
    {
        return $this->_errorObj;
    }

    function registerMessageObject( &$obj )
    {
        $this->_messageObj = &$obj;
    }

    function &getMessageObject()
    {
        return $this->_messageObj;
    }

    function anyErrorOrMessage()
    {
        $error =& $this->getErrorObject();
        $message =& $this->getMessageObject();
        // check if any errors occured
        // if so we show them in the main.tpl
        // we have to check this here, because all pages have been processed upto this point!
        // we must not check it in main.php!!!
        if( $this->anyError() || $this->anyMessage() )
        {
            return true;
        }
        return false;
    }

    function anyError()
    {
        $error =& $this->getErrorObject();

        if( ($this->isLiveMode() && $error->existAnyText()) ||
            (!$this->isLiveMode() && $error->existAny()) )
        {
            return true;
        }
        return false;
    }

    function anyMessage()
    {
        $message =& $this->getMessageObject();

        if( ($this->isLiveMode() && $message->existAnyText()) ||
            (!$this->isLiveMode() && $message->existAny()) )
        {
            return true;
        }
        return false;
    }    
    
    function getErrors()
    {
        global $util;   // this sucks !!!!

        $error = &$this->getErrorObject();
        $errors =  $this->isLiveMode() ? $error->getAllText(null) : $error->getAll(null);
                
        $ret = '';
        foreach( $errors as $aError )
        {
            if( is_object($util) && method_exists( $util,'translate' ) )   // this sucks tooo !!!!
                $ret .= $util->translate($aError).'<br>';
            else
                $ret .= $aError.'<br>';
        }
        return $ret;
    }

    function getMessages()
    {
        global $util;   // this sucks !!!!
        
        
        $ret = '';	// AK : Oh well ...
        $message = &$this->getMessageObject();
        $messages = $this->isLiveMode() ? $message->getAllText(null) : $message->getAll(null);

        foreach( $messages as $aMessage )
            if( is_object($util) && method_exists( $util,'translate' ) )     // this sucks tooo !!!!
            {
                $ret .= $util->translate($aMessage).'<br>';
            }
            else
                $ret .= $aMessage.'<br>';
        return $ret;
    }

} // end of class
?>
