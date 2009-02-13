<?php
//
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2002 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Wolfram Kriesing <wolfram@kriesing.de>                      |
// +----------------------------------------------------------------------+
//

/**
*
*   $Log: common.php,v $
*   Revision 1.3  2003/03/11 12:57:56  wk
*   *** empty log message ***
*
*   Revision 1.21  2003/03/11 10:23:35  mccain
*   - enhance Logging in expire method
*
*   Revision 1.20  2003/02/02 20:58:49  mccain
*   - use # instead of ~ for regexp delimiter, since ~ an appear in a url
*
*   Revision 1.19  2003/02/02 20:54:26  mccain
*   - added logging, use option logFile
*   - clean up the session handling, remove global-use
*   - fix some win-issues
*
*   Revision 1.18  2003/01/28 19:41:37  mccain
*   - added ignoreForHash
*   - solve some E_ALL warnings
*
*   Revision 1.17  2003/01/27 15:03:16  mccain
*   - HTTPS bugfix, detection didnt work
*
*   Revision 1.16  2003/01/18 15:25:34  mccain
*   - prevent some E_ALL warnings
*   - replace # by //
*
*   Revision 1.15  2002/09/28 14:50:00  mccain
*   - bugfix, for properly working on the DOCUMNET_ROOT, reported by  Ruben Vanhoutte - thanks
*
*   Revision 1.14  2002/09/26 09:55:22  mccain
*   - added option autoAuth, so you can also turn it off explicitly
*   - use _SESSION instead of session-functions
*
*   Revision 1.13  2002/09/18 14:08:12  mccain
*   - added digest which is customizable
*
*   Revision 1.12  2002/08/20 14:33:19  mccain
*   - made setData accept an array too
*
*   Revision 1.11  2002/06/03 14:02:47  mccain
*   - added method setData, to be able to write data in the auth-session
*
*   Revision 1.10  2002/05/28 10:02:12  mccain
*   - make it possible to get the last used username (also in case the login failed)
*   - make it possible to use multiple instances of the auth class on one page
*   - bugfixed and enhanced the isProtectedUrl - check
*
*   Revision 1.9  2002/05/24 16:09:33  mccain
*   - save the query string of a requested url too (the GET data)
*
*   Revision 1.8  2002/05/24 13:33:01  mccain
*   - oops, forgot to save :-)
*
*   Revision 1.7  2002/05/24 13:31:32  mccain
*   - set also complete url for all header-location's
*
*   Revision 1.5  2002/05/24 12:54:33  mccain
*   - some adjustments for PHP >4.1, versions below not tested
*
*   Revision 1.4  2002/03/18 22:05:18  mccain
*   - implemented md5 digest
*   - added verifyLoginMethod option, experimental
*
*   Revision 1.3  2002/02/26 15:19:42  mccain
*   - fixed bug in isProtected
*   - removed old stuff, but needs check
*
*   Revision 1.2  2002/02/07 22:06:46  mccain
*   - added informational comment
*
*   Revision 1.1.1.1  2002/02/07 17:26:33  mccain
*
*   ##### those are my local revisions, from before moving it to sourceforge :-) #####
*   ##### just kept for informational reasons, might be removed one day
*
*   Revision 1.25  2002/02/07 03:13:26  cain
*   - added new option 'dontProtect'
*   - optimized isUrlProtected method
*
*   Revision 1.24  2002/02/07 02:23:08  cain
*   - corrected bug in detecting current protocol, now https also works properly
*   - filenames to protect can also look like this now: '*.php', 'view*.php' ...
*   - now auto-auth also works on a server root
*
*   Revision 1.23  2002/02/01 06:59:55  cain
*   - die on redirect, so nothing is executed that needs auth
*
*   Revision 1.22  2002/01/21 23:01:53  cain
*   - added license statement
*
*   Revision 1.21  2002/01/15 11:25:48  cain
*   - debugging
*
*   Revision 1.20  2001/12/15 14:07:40  cain
*   - allow not protected pages to be the loginPage
*
*   Revision 1.19  2001/12/13 10:31:24  cain
*   - made autoAuth work also on pages which are not protected
*
*   Revision 1.18  2001/11/11 17:35:07  cain
*   - removed some comment
*
*   Revision 1.17  2001/10/31 21:03:49  cain
*   - extended the method getData to be able to return all the persistent data
*
*   Revision 1.16  2001/10/31 00:20:12  cain
*   - moved the parameters copy here from DB, since it is common
*
*   Revision 1.15  2001/10/30 21:54:52  cain
*   - decided to call the method which finally accesses the data source '_login' (before it was authenticate) so the association is easier
*
*   Revision 1.14  2001/10/29 23:11:55  cain
*   - added package name
*
*   Revision 1.13  2001/10/28 21:39:07  cain
*   - remove 'expire' TODO, since i implemented it :-)
*
*   Revision 1.12  2001/10/28 11:46:07  cain
*   - remember the requested page on expiry
*
*   Revision 1.11  2001/10/28 11:22:27  cain
*   - added expire stuff
*
*   Revision 1.10  2001/10/27 19:13:27  cain
*   - added some options, to be more configurable :-)
*
*   Revision 1.9  2001/10/27 16:45:50  cain
*   - use property sessionArrayName instead of option sessionArray, since we make the sessionArrayName unambigious
*   - fixed some bugs in login and autoAuth
*
*   Revision 1.8  2001/10/26 11:58:25  cain
*   - use unique session name, to run multiple authentication on one server
*   - some bugfix in the login method
*
*   Revision 1.7  2001/10/25 18:28:06  cain
*   - corrected login and logout
*   - updated some of the GLOBALS stuff
*   - some beautifying
*
*   Revision 1.6  2001/10/25 16:59:26  cain
*   - put autoAuth feature in extra method
*   - added some phpdoc comments
*
*   Revision 1.5  2001/10/25 16:32:03  cain
*   - save all variables returned by authenticate mthod
*
*   Revision 1.4  2001/10/25 15:58:25  cain
*   - first well working auto login version
*
*   Revision 1.3  2001/10/24 17:25:44  cain
*   - added getRequestedUrl, isUrlProtected
*   - added property sessionArray
*
*   Revision 1.2  2001/10/17 21:04:52  cain
*   - first version which works with auto auth
*
*
*/


/**
*   this is an abstract class it can not be used without being extended
*
*   @TODO   -   max login tries
*           -   digests, simple md5, challenge response - need a mechanism of how to simply implement any digest
*   @package    Auth
*
*/

class Auth_common extends PEAR
{

    /**
    *   is set to true if the auth expired, but only valid during on page-run
    *   to be able to call isExpired multiple times on one page
    *
    *   @access private
    *   @var    string  expired
    */
    var $expired = false;

    /**
    *   this is used temporarily by getting the protocol and the host
    *   to form entire url's when needed, value is like: "http://localhost"
    *
    *   @access public (read only)
    *   @var    string  urlPrefix
    */
    var $urlPrefix;

    /**
    *   the name for the session array
    *
    *   @access private
    *   @var    string  sessionArrayName
    */
    var $sessionArrayName;

    /**
    *
    *   @access via setOptions
    *   @var    array   options
    */
    var $options = array(
                            'sessionArrayPrefix' => '_auth',// the array name for the data which we save in the session,
                                                            //  we concatenate an unambigious string to this, see setup
                            'optimize'      => 'performance', // 'performance' or 'portability'
                            'debug'         => 0,   // numeric debug level
                            'expire'        => 0,   // the time after which the authentication expires (in seconds)
                                                    // the expiration only occurs if the user was not active for 'expire' time
                                                    // 0 means never expires
                            'digest'        => 'md5',
// 'loginVerifyMethod' feature is just experimental
                            'loginVerifyMethod'=>'',// this (array of) method(s) is called additionally on login

                            //'afterLogin'    =>  array(),    // give any number of function/methods to call after successful login
                                                            // if i call it here in the auth-class there is no auth-object yet,
                                                            // since it gets returned by setup, but mostly the auth data are needed in such methods


                            // options for the autoAuth feature, all the following
                            'inputPassword' => '_auth_password',  // the input-field name prefix for password
                            'inputUsername' => '_auth_username',  // the input-field name prefix for username

                            // protect options
                            'autoAuth'      => true,    // if you set this explicitly to false
                            'protectRoot'   => '',  // the root path under which the index refers to ($HTTP_HOST/root)
                                                    // NOTE: there is NO '/' (slash) at the end
                                                    // ONLY: for protecting the server root, set the value to '/'
                            'protect'       => '/', // paths or single pages that shall be preotected, like ("/protect.php","/protectAll/*")
                                                    // by default all under the protectRoot is protected (that's what '/' means)
                            'dontProtect'   => '',  // this can be an array of pages, which are in the protect root
                                                    // but dont need to be protected, like CSS-files, etc.

                            // auto-auth pages
                            'loginPage'     => 'login.php',// the login page relative to the protectRoot
                                                    // if you use 'smartProtect' remember not to use a value like '../login.php'
                            'errorPage'     => 'error.php',// the error page, shown upon login failure, relative to protectRoot
                            'expirePage'    => '',  // the expire page, shown when the authentication has expired, relative to protectRoot
                                                    // if no expire page is given the login page will be shown, this is default
                            'defaultPage'   => 'login.php',// if loginPage is shown in the browser but no other URL was requested before
                                                           // means, if we have nothing in the session under 'requestedUrl'
                                                           // the default page is the login page to request login automatically if not logged in yet
                            // other auto-auth features
                            'smartProtect'  => true,// if this is set to true loginPage and errorPage
                                                    // will not be protected also if they are under the 'protectRoot'-directory
                            'loginDataSource'=> '_POST'   // the array name in GLOBALS where the login data (username, password) are in
                                                           // if you use a form to submit the login data its strongly suggested that
                                                           // you use the default, which are the POST-variables
                            ,'unsetLoginData'=>  true   // if true this unsets the login data after logging in, so that those variables are still
                                                        // available in GLOBALS but not in GLOBALS['HTTP_POST_VARS'] afterwards is PHP-specific
                                                        // may be in some later version we also unset them there ... dont know yet
                                                        // may be this feature is just non-sense anyway
                            ,'ignoreForHash' =>  array()    // set the names of the options, that shall be ignored
                                                            // for the options-hash, which is made for allowing
                                                            // multiple instances on one page
                            ,'logFile'      =>  false   // set it to a writeable filename, to log what happens
                        );

    /**
    *   @var    string  a hash over the options currently used
    */
    var $_optionsHash = null;

    /**
    *   the instance of the Log-class
    */
    var $_log = null;

    /**
    *
    *   @access public
    *   @author Wolfram Kriesing <wolfram@kriesing.de>
    */
    function Auth_common()
    {
        $this->PEAR('Auth_Error');
        $this->features = array();
        $this->errorcode_map = array();
    }

    /**
    *   does the auto-auth if the options 'protect' and 'protectRoot' are given
    *   if you overwrite this method dont forget to call the parent method via parent::setup( $dsninfo )
    *
    *   @access public
    *   @author Wolfram Kriesing <wolfram@kriesing.de>
    *   @return mixed   nothing, Auth_Error-object
    */
    function setup( $dsninfo )
    {
/*
//FIXXME check if we really dont need this anymore
        // if options are given in the dsninfo set them here
        // I THINK, this is only of use for classes which extend Auth_common, since they
        // mostly use the dsninfo['parameters']
        // but for that we dont have to do it in every subclass we do it here :-)
        if( sizeof($dsninfo['parameters']) )
        foreach( $dsninfo['parameters'] as $paraName=>$aParameter )
        {
            $err = $this->setOption($paraName,$aParameter);
                if( Auth::isError($err) )
                    return $err;
        }
*/

        // go thru the ignoreForHash-option, which tells which options
        // are not relevant for the options-hash
        $optionData = $this->options;
        if (isset($this->options['ignoreForHash'])) {
            settype($this->options['ignoreForHash'],'array');
            foreach ($this->options['ignoreForHash'] as $aIgnore) {
                unset($optionData[$aIgnore]);
            }
        }
        // create the hash over the options, since we need it in some places
        // and if we change it (what autoAuth does!!! no good thing, change it one day)
        // i.e. getInputUsername returns different results after we have changed the options
        // array, so hash it before we modify it!
        $this->_optionsHash = md5(serialize($optionData));

        // setup an unambigious array name in case someone wants to use the
        // authentication on one server in the same session for different web pages
        // this way we avoid a user being authenticated on all of them
        // its better to set the _array_name_ using the md5 (instead of the _session_name_),
        // since some apps may be want to mess with the session themselves :-)   YES
        $this->sessionArrayName = $this->options['sessionArrayPrefix'].
                                    md5( //implode(':',$dsninfo).
//FIXXME removed this, since the new constructor now doesnt necesarily send a dsninfo-arry
                                        // use _optionsHash instead of implode($this->options)
                                        // for the options to prevent Notice 'array to string conversion' (with E_ALL)
                                        // since there is an array in the options array
                                        $this->_optionsHash.$dsninfo['phptype'] );
//$this->_log('Auth/common : ',print_r($_SESSION,true),'SESSION',__LINE__);
//$this->_log('Auth/common : ',print_r($this->sessionArrayName,true),'SESSION-array_name',__LINE__);

		  //AK Check if session already existing ...
        if(!isset($_SESSION)) {
		    session_name('sid'.preg_replace('/<.*>|[^a-z0-9]/i','',$config->applName));
		    session_start();
        }
        $this->_session = &$_SESSION[$this->sessionArrayName];

        // set this reference to the globals, so the session vars can be referenced
        // anywhere in the class by using "$GLOBALS[$sessArray]"

        // get the urlPrefix, which might be used throughout this class, so we dont need to do it again :-)
        // default is 'http://'
//FIXXME if there are other protocols then those 2 we would be f..cked, correct it then, but i dont know any yet that could use this class
        $this->urlPrefix = 'http://'.$_SERVER['HTTP_HOST'];
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $this->urlPrefix = 'https://'.$_SERVER['HTTP_HOST'];
            $this->_log('running on HTTPS','setup',__LINE__);
        }

        // is the auto-auth feature activated? are protected pages given?
        if( $this->options['autoAuth'] && $this->options['protect'] && $this->options['protectRoot'])
        {
            $this->_log('call autoAuth','setup',__LINE__);
           
            $err = $this->autoAuth();          
            if(Auth::isError( $err )) {
                $this->_log('ERROR, autoAuth returned an error','setup',__LINE__);
                return $err;
            }
        }

        $this->isExpired();     // set expired-property in case the auth expired
    }

    /**
    *   authenticate the user if necessary
    *
    *   @access private
    *   @author Wolfram Kriesing <wolfram@kriesing.de>
    *   @param  string  $username
    *   @param  string  $password
    *   @param  integer AUTH_FAILED or AUTH_OK or Auth_Error-object
    */
    function login( $username , $password )
    {
        $this->_lastUsedUsername = $username;
//print "dos is et: $username , $password<br>";
        // if THE SAME user is already logged in dont need to run authenticate
// this might be wrong, for the case that teh same user logs in BUT with a wrong password
// what is the policy then?
// TODO check expire
        if (isset($this->_session['loggedIn']) && $this->_session['loggedIn'] == true &&
            isset($this->_session['username']) && $this->_session['username'] == $username) {
            $this->_log('login OK, use data from session','login',__LINE__);
            return AUTH_OK;
        }

        $this->logout();                            // reset all data in case a different user logs in        
        $res = $this->_login( $username , $password );
        if (Auth::isError( $res )) {
            $this->_log('ERROR, _login failed','login',__LINE__);
            return $res;
        }
        // authenticate can also return an array which contains all the data that shall be made persistent
        if ($res != AUTH_OK && !is_array($res)) {
            $this->_log('no valid login','login',__LINE__);
            return AUTH_FAILED;
        }

        // if many data, such as role, group or whatever are returned by the authenticate method
        // save those data in the session array, so it can be accessed via the getData-method
        // NOTE: if you dont want the password to be saved in the session erase it from the array in the _login-method
        if (is_array($res)) {
            foreach ($res as $key=>$aDataField) {   // foreach through the array not to erase other already saved data
                if (!isset($this->_session[$key])) {// if a data field already has a value dont overwrite it
                    $this->_session[$key] = $aDataField;
                }
            }
        }
        $this->_session['username'] = $username;

        if ($this->options['loginVerifyMethod']) {
// FIXXME loop through array of 'loginVerifyMethod' and check if is object
// passing the $res is bullshit, but when using $auth in the loginVerifyMethod function
// there are no values in $auth yet ... :-(
            if (call_user_func( $this->options['loginVerifyMethod'] , $res ) == false) {
                $this->_session= array();
                return AUTH_FAILED;
            }
        }

        // set the login variables in the session
        $this->_session['loggedIn'] = true;
        if ($this->options['expire'] > 0) {
            $this->_session['timestamp'] = time();
        }
        $this->_log('login OK','login',__LINE__);
        return AUTH_OK;
    }

    /**
    *   logout
    *
    *   @access public
    *   @author Wolfram Kriesing <wolfram@kriesing.de>
    */
    function logout()
    {
        $this->_log('logout called','logout',__LINE__);
        // unset varibables which identify the user as logged in
        // erase all session data, no need to save 'loggedIn', since we only check for 'true' not on 'false'
        $this->_session= array();
    }

    /**
    *   this method tells either the authentication has expired
    *   if the authentication expires, then this method logs out the user
    *   and a next call to this method would not result in 'true' since the
    *   user is not logged in
    *
    *   @access public
    *   @author Wolfram Kriesing <wolfram@kriesing.de>
    */
    function isExpired()
    {
        // we should set a property "expired" to true
        // if the auth expired, so one can call isExpired and gets true for multiple requests to isExpired, but only during ONE page
        if ($this->expired == true) {
            return true;
        }

        // if the user is not logged in we can not tell if his authentication has expired, since we have
        // destroyed all his session data. that's the policy, might change?
        if ($this->isLoggedIn() && $this->options['expire']>0) {
            $this->_log('check expired','isExpired',__LINE__);
            if( ($this->_session['timestamp'] + $this->options['expire']) < time()) {
                $this->_log('auth has expired','isExpired',__LINE__);
                $this->_log('timestamp: '.$this->_session['timestamp'],'isExpired',__LINE__);
                $this->_log('expire-time: '.$this->options['expire'],'isExpired',__LINE__);
                $this->_log('time(): '.time(),'isExpired',__LINE__);
                $this->_log('time()-timestamp+expire: '.(time()-$this->_session['timestamp']+$this->options['expire']),'isExpired',__LINE__);
                
                $this->logout();
                $this->expired = true;
                return true;
            }
            $this->_session['timestamp'] = time();
        }
        return false;
    }

    /**
    *   is the current user already logged in?
    *
    *   @access public
    *   @author Wolfram Kriesing <wolfram@kriesing.de>
    *   @param  boolean true if loggedIn
    */
    function isLoggedIn()
    {
        if (@$this->_session['loggedIn'] == true) {
            return true;
        }
        return false;
    }

    /**
    *   auto authenticate if requested
    *   this method does all the authenticate stuff needed to authorize for
    *   a requested page if the user is not logged in
    *
    *   @access private
    *   @author Wolfram Kriesing <wolfram@kriesing.de>
    *   @return nothing or Auth_Error-object
    */
    function autoAuth()
    {
        $loginDataSource =  $GLOBALS[$this->options['loginDataSource']];

       
        // use @ to prevent E_ALL warning 'undefined index'
        $_inputUsername = $this->getInputUsername();
        $_inputPassword = $this->getInputPassword();
        $loginUsername = isset($loginDataSource[$_inputUsername])?$loginDataSource[$_inputUsername]:false;
        $loginPassword = isset($loginDataSource[$_inputPassword])?$loginDataSource[$_inputPassword]:false;
        
        // if a server's root shall be protected, protectRoot is only '/'
        // but this is only to set a value, since this is the criteria by which we
        // detect if autoAuth is on, or not and for a server root, it actually would be
        // no vlaue, but then we cant detect the autoAuth :-) .. got a better way?
        if( $this->options['protectRoot']=='/' )
            $this->options['protectRoot'] = '';

        if( $this->isUrlProtected() ||              // is the current url protected
// FIXXME i dont know if those following two lines dont make this insecure, i didnt test it enough either
// but it works, so you can also log in on pages, which are not protected (as set in the options)
            ( $loginUsername && $loginPassword )    // are username and password given?
          )
        {     
            // check if login data are sent BEFORE we check isLoggedIn, in case the currently logged in
            // user has directly gone to the loginPage without logging out
            if ($loginUsername && $loginPassword) { // if login data are given, username and password
                $this->_log('username and password are given','autoAuth',__LINE__);
                // get the requested url in case AUTH_FAILED we can put it in the session again, since login always first destroys the session data
                $reqUrl = $this->getRequestedUrl();

                $res = $this->login($loginUsername , $loginPassword );  // then login automatically
                if (Auth::isError($res)) {
                    $this->_log('ERROR, login returned an error','autoAuth',__LINE__);
                    return $res;
                }

                if (AUTH_OK != $res) {
                    // since autoAuth is only a feature we dont leave requestedUrl in the session by default
                    // but we still want the user to get back to his requested URL
                    $this->_session['requestedUrl'] = $reqUrl;
                    // remember the last used username too, so getUsername works also if the login failed
                    // to be able to show it in the form again, if wanted so
                    $this->_session['username'] = $this->_lastUsedUsername;
                    // if login fails redirect to errorPage, and leave autoAuth
                    $url = $this->urlPrefix.$this->options['protectRoot'].'/'.$this->options['errorPage'];
                    $this->_log('no valid login, redirect to: '.$url,'autoAuth',__LINE__);
                    header("Location: $url");
                    die();  // die here so no action takes place which actually needs to be authorized
                    //return;
                }

                if ($this->options['unsetLoginData'] == true) {
                    // unset the login variables
                    unset( $GLOBALS[$this->options['loginDataSource']][$this->getInputUsername()] );
                    unset( $GLOBALS[$this->options['loginDataSource']][$this->getInputPassword()] );
                }
            }

            // build the requested url, add the query string if one is given
            $requestedUrl = $this->urlPrefix.$_SERVER['PHP_SELF'].
                            ( $_SERVER['QUERY_STRING']?'?'.$_SERVER['QUERY_STRING']:'' );
            // check expiration, if expired redirect to expiredPage,
            // right after login the page actually should never expire, so we can check it here
            if ($this->isExpired()) {
                // if an expiredPage is given go there
                if ($this->options['expirePage']) {
                    $this->_session['requestedUrl'] = $requestedUrl;
                    $url = $this->urlPrefix.$this->options['protectRoot']."/".$this->options['expirePage'];
                    $this->_log('auth has expired, redirect: '.$url,'autoAuth',__LINE__);
                    header("Location: $url");
                    die();  // die here so no action takes place which actually needs to be authorized
                    return;
                }
                $this->_log('auth has expired'.$url,'autoAuth',__LINE__);
            }

			//$this->_log('this='.print_r($this->_session,true),'autoAuth',__LINE__);
			

            if (!$this->isLoggedIn()) {
                $this->_log('isLoggedIn() is false, requestedUrl='.$requestedUrl,'autoAuth',__LINE__);
                $this->_session['requestedUrl'] = $requestedUrl;
// TODO pass on all the POST vars too ... but how, if they were posted its not a good idea to put them in hidden form fields
                // go to the loginPage if the user is not authenticated
                $url = $this->urlPrefix.$this->options['protectRoot']."/".$this->options['loginPage'];
                $this->_log('redirect: '.$url,'autoAuth',__LINE__);
                header("Location: $url");
                die();  // die here so no action takes place which actually needs to be authorized
            }
        }
    }

    /**
    *   check if the given url is protected by this auth class, if no url given uses $GLOBALS['PHP_SELF']
    *
    *   @access public
    *   @author Wolfram Kriesing <wolfram@kriesing.de>
    *   @param  string  $url    the url to check for protection
    *   @return boolean true/false
    */
    function isUrlProtected( $url=null )
    {
        if( $url===null )
            $url = $_SERVER['PHP_SELF'];

        $root = $this->options['protectRoot'];
        // remove trailing '/' so we know we put it there by hand
        $root = preg_replace('#/$#','',$root);
        // if we are on a win-system, we better replace backslashes by slashes
        // so we can be sure that we are only working with slashes here
        if (DIRECTORY_SEPARATOR=='\\') {
            $root = str_replace('\\','/',$root);
        }

        $protect = $this->options['protect'];
        settype($protect,'array');
        // only if any is given, otherwise make it an empty array
        $dontProtect = $this->options['dontProtect'] ? $this->options['dontProtect'] : array();
        settype($dontProtect,'array');

        $regStrings = array();
        // check if $url is protected, leave out protocol check, can also be "https" or ...
        foreach( $protect as $aProtectedUrl )
        {
            // if we are on a win-system, we better replace backslashes by slashes
            if (DIRECTORY_SEPARATOR=='\\') {
                $aProtectedUrl = str_replace('\\','/',$aProtectedUrl);
            }
            // remove leading '/' so we know we put it there by hand
            $aProtectedUrl = preg_replace('#^/#','',$aProtectedUrl);
            $aProtectedUrl = preg_quote($aProtectedUrl);    // quote the expression, so the regExp works properly
            // unquote the '*' and put a '.' in front, so we have a correct regExp which also
            // finds stuff like: 'view*.php', '*.php', '*.css.*'
            $aProtectedUrl = preg_replace( "/(\\\)(\*)/" , '.*' , $aProtectedUrl );
            $regStrings[] = "($root/$aProtectedUrl)";
        }
        $regString = '#'.implode( '|' , $regStrings).'#i';
//print('protected: '.$regString.'<br>');

        // if 'smartProtect' is on login and error page are not protected
        if( $this->options['smartProtect']==true )
        {
            $dontProtect[] = $this->options['loginPage'];
            if( $this->options['errorPage'] )
                $dontProtect[] = $this->options['errorPage'];
            if( $this->options['expirePage'] )
                $dontProtect[] = $this->options['expirePage'];
        }

        // get all the pages which dont need to be protected and make a regExp
        $regStrings = array();
        foreach( $dontProtect as $aNotProtectedUrl )
        {
            // remove leading '/' so we know we put it there by hand
            $aNotProtectedUrl = preg_replace('#^/#','',$aNotProtectedUrl);
            // quote the expression, so the regExp works properly
            $aNotProtectedUrl = preg_quote($aNotProtectedUrl);
            // unquote the '*' and put a '.' in front, so we have a correct regExp which also
            // finds stuff like: 'view*.php', '*.php', '*.css.*'
            $aNotProtectedUrl = preg_replace( "/(\\\)(\*)/" , '.*' , $aNotProtectedUrl );

            $completeUrl = $root.'/'.$aNotProtectedUrl;
/*            if(substr($root,-1)!='/' && substr($aNotProtectedUrl,0,1)!='/' )    // if there are no slashes, add the slash
            {
                $completeUrl = $root.'/'.$aNotProtectedUrl;
            }
*/
            $regStrings[] = "($completeUrl)";
        }
        $regStringDontProtect = '#'.implode( '|' , $regStrings).'#i';
//print('NOT protected regExp-String: '.$regStringDontProtect.'<br>');
//print "URL to check: $url<br>";

//print "preg_match( $regString , $url ) = ";
//print_r(preg_match( $regString , $url ));
//print "<br><br>";
//$regString = '~(/contax/htdocs/modules)~i';
        if( preg_match( $regString , $url ) == true )
        {
            // if we are on a protected page, check if it is in 'dontProtect',
            // then we return false, since the current page doesnt need protection then
//print "preg_match( $regStringDontProtect , $url ) = ";
//print_r(preg_match( $regStringDontProtect , $url ));print "<br><br>";
            if( preg_match( $regStringDontProtect , $url ) == true )
            {
                $this->_log('url is in dontProtect','isUrlProtected',__LINE__);
//print("is not protected<br>");
                return false;
            }
//print("is protected: $regString<br>");
            $this->_log('url is protected','isUrlProtected',__LINE__);
            return true;
        }
//print("is not protected 1<br>");       
        $this->_log('url is not protected','isUrlProtected',__LINE__);
        return false;
    }

    /**
    *   return the auth-data requested
    *   string might be 'username' or any user defined value, since every
    *   value returned by _login is saved in the session
    *
    *   @access public
    *   @author Wolfram Kriesing <wolfram@kriesing.de>
    *   @param  string  $string
    *   @return mixed
    */
    function getData( $string=null )
    {
        // if no certain field is requested return all
// TODO may be better if we save the data returned by _login in an seperate array
// and return only that array, like: $GLOBALS[$this->sessionArrayName]['userData']
// since in $GLOBALS[$this->sessionArrayName] we also have stuff like: 'requestedUrl', etc.
        if ($string===null) {
            return $this->_session;
        }
        // AK : Added check if given value is in array 
	     if (isset($this->_session[$string]))
	        return $this->_session[$string];
	     else
	        return $this->_session;
    }

    /**
    *   save some data in the auth-session
    *   this is needful when you want to save some data, which are i.e.
    *   exclusively available during a (authorized) session
    *
    *   @access public
    *   @author Wolfram Kriesing <wolfram@kriesing.de>
    *   @param  mixed       the key under which this value is saved
    *                       or an array with key-value pairs that shall be added
    *   @param  mixed       the value for the key to be saved
    */
    function setData( $stringOrArray , $value=null )
    {
        $data = array();
        if( is_string($stringOrArray) )
            $data[$stringOrArray] = $value;
        else
            $data = $stringOrArray;

        foreach( $data as $key=>$val )
            $this->_session[$key] = $val;
    }
    /**
    *   return the username when logged in successfully, if not too :-) but its empty
    *
    *   @access public
    *   @author Wolfram Kriesing <wolfram@kriesing.de>
    *   @param  string  $password
    *   @param  string  encrypted (digested) password
    */
    function getUsername()
    {
        //$username = ;
        return $this->getData('username');//$username ? $username : $this->_lastUsedUsername;
    }

    /**
    *   return the username when logged in successfully, if not too :-) but its empty
    *
    *   @access public
    *   @author Wolfram Kriesing <wolfram@kriesing.de>
    *   @param  string  $password
    *   @param  string  encrypted (digested) password
    */
    function getRequestedUrl()
    {
        // if no requestedUrl is given, use the default page
        if( !isset($this->_session['requestedUrl']) ) {
            return $this->urlPrefix.$this->options['protectRoot'].'/'.$this->options['defaultPage'];
        }

        return $this->_session['requestedUrl'];
    }

    /**
    *   get the input-field name for the password
    *
    *   @access public
    *   @author Wolfram Kriesing <wolfram@kriesing.de>
    *   @return string  form's input-field name for passord
    */
    function getInputPassword()
    {
        return $this->options['inputPassword'].$this->_optionsHash;
    }

    /**
    *   get the input-field name for the username
    *
    *   @access public
    *   @author Wolfram Kriesing <wolfram@kriesing.de>
    *   @return string  form's input-field name for username
    */
    function getInputUsername()
    {
        return $this->options['inputUsername'].$this->_optionsHash;
    }

    /**
    *   get the complete url for the login page
    *
    *   @access public
    *   @author Wolfram Kriesing <wolfram@kriesing.de>
    *   @return string  the login-Url
    */
    function getLoginUrl()
    {
        return $this->urlPrefix.$this->options['protectRoot'].'/'.$this->options['loginPage'];
    }

    /**
    *   return the digested password
    *   use the digest configured
    *
    *   @access private
    *   @author Wolfram Kriesing <wolfram@kriesing.de>
    *   @param  string  $password
    *   @param  string  encrypted (digested) password
    */
    function digest( $username , $password )
    {                  
        switch( $this->options['digest'] )
        {
            case 'md5': $password = md5( $username.$password );
                        break;
            case '':
            case 'none':// leave the password as it is, not recommended
                        $password = $password;
                        break;
            default:    eval("\$password=".$this->options['digest'].';');
                        break;
        }
                         
        return $password;
    }

    /**
    *   do whatever needed when an option is set
    *   !!! to be overwritten !!!
    *
    *   @access public
    *   @author Wolfram Kriesing <wolfram@kriesing.de>
    *   @param  boolean true if loggedIn
    */
    function setOption( $option , $value )
    {
        if (isset($this->options[$option])) {
            $this->options[$option] = $value;
            return AUTH_OK;
        }
        return $this->raiseError("unknown option $option");
    }

    /**
    * This method is used to communicate an error and invoke error
    * callbacks etc.  Basically a wrapper for PEAR::raiseError
    * without the message string.
    *  adapted from DB::common
    *
    *  @see    PEAR::PEAR_Error
    *   @access private
    * @author  Stig Bakken <ssb@fast.no>
    * @param mixed    integer error code, or a PEAR error object (all
    *                 other parameters are ignored if this parameter is
    *                 an object
    *
    * @param int      error mode, see PEAR_Error docs
    *
    * @param mixed    If error mode is PEAR_ERROR_TRIGGER, this is the
    *                 error level (E_USER_NOTICE etc).  If error mode is
    *                 PEAR_ERROR_CALLBACK, this is the callback function,
    *                 either as a function name, or as an array of an
    *                 object and method name.  For other error modes this
    *                 parameter is ignored.
    *
    * @param string   Extra debug information.  Defaults to the last
    *                 query and native error code.
    *
    * @param mixed    Native error code, integer or string depending the
    *                 backend.
    *
    * @return object  a PEAR error object
    *
    * @see PEAR_Error
    */
    function &raiseError($code = AUTH_ERROR, $mode = null, $options = null,
                         $userinfo = null, $nativecode = null)
    {
        // The error is yet a AUTH error object
        if (is_object($code)) {
            return PEAR::raiseError($code, null, null, null, null, null, true);
        }

        if ($userinfo === null) {
            $userinfo = @$this->last_query;
        }

        if ($nativecode) {
            $userinfo .= " [nativecode=$nativecode]";
        }

        return PEAR::raiseError(null, $code, $mode, $options, $userinfo,
                                  'Auth_Error', true);
    }

    /**
    *   log various messages
    *
    */
    function _log($msg,$method=0,$line=0)
    {
        if (!$this->options['logFile']) {
            return;
        }

        if (!isset($this->_log)) {
            require_once 'Log.php';
            $this->_log = &Log::factory('file',$this->options['logFile']);
            $this->_log->log('-----START----');
            $this->_log->log($_SERVER['PHP_SELF']);
        }

        $this->_log->log('[Auth'.($method?"::$method":'')."] $msg".($line?" ($line)":''));
    }

}
?>
