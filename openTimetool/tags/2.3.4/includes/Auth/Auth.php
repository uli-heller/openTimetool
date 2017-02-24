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
 *	$Log: Auth.php,v $
 *	Revision 1.3  2003/03/11 12:57:56  wk
 *	*** empty log message ***
 *	
 *	Revision 1.8  2002/11/27 16:48:58  mccain
 *	- throw error if auth-type is unknown
 *	
 *	Revision 1.7  2002/08/20 19:17:37  mccain
 *	- added NIS-error
 *	
 *	Revision 1.6  2002/08/20 12:57:50  mccain
 *	- added NIS-Error-constant
 *	
 *	Revision 1.5  2002/06/19 21:44:52  mccain
 *	- added Auth::isError again, just to be sure that this version still works, to be tested
 *	
 *	Revision 1.4  2002/06/19 21:42:39  mccain
 *	- check using PEAR::isError
 *	
 *	Revision 1.3  2002/02/07 22:06:46  mccain
 *	- added informational comment
 *	
 *	Revision 1.2  2002/02/07 18:17:19  mccain
 *	*** empty log message ***
 *	
 *	Revision 1.1.1.1  2002/02/07 17:26:33  mccain
 *	
*   ##### those are my local revisions, from before moving it to sourceforge :-) #####
*   ##### just kept for informational reasons, might be removed one day
 *
 *	Revision 1.13  2002/02/07 05:31:03  cain
 *	- confirm to PEAR standard setup-call
 *	
 *	Revision 1.12  2002/01/21 23:01:53  cain
 *	- added license statement
 *	
 *	Revision 1.11  2002/01/16 13:44:04  cain
 *	- FIXXME comment
 *	
 *	Revision 1.10  2001/11/11 17:34:18  cain
 *	- added the feature that you can specifiy your own class in the URL
 *	
 *	Revision 1.9  2001/10/31 00:22:22  cain
 *	- whitespace
 *	
 *	Revision 1.8  2001/10/29 23:12:21  cain
 *	- added package name
 *	
 *	Revision 1.7  2001/10/29 22:48:49  cain
 *	- include proper files without 'Auth_' before
 *	
 *	Revision 1.6  2001/10/28 11:23:42  cain
 *	*** empty log message ***
 *	
 *	Revision 1.5  2001/10/27 18:08:45  cain
 *	- if class name starts with DB use the class Auth_DBxxx instead of using authtype.phptype
 *	
 *	Revision 1.4  2001/10/27 16:43:30  cain
 *	- added some comments
 *	- added error constants
 *	
 *	Revision 1.3  2001/10/26 11:59:22  cain
 *	- if authtype different then DB include the proper file and construct an object of this class
 *
 *	Revision 1.2  2001/10/17 21:06:53  cain
 *	- call setup in constructor
 *
 *	Revision 1.1  2001/10/17 17:57:58  cain
 *	*** empty log message ***
 *
 */

// we need this for the parseDNS
require_once("DB.php");


/*
*   remember:   a "database" is not only a database in the common sense, such as mysql or oracle
*               here "database" means any source of data, which depends on the 'authtype', might
*               be LDAP, DB, Memory or whatever
*
*/
define("AUTH_OK",                       1);

define("AUTH_ERROR",                    -1);
// requested class not found
define("AUTH_ERROR_NOT_FOUND",          -2);
define("AUTH_ERROR_DB_CONNECT_FAILED",  -3);    // could not connect to 'database'
define("AUTH_ERROR_DB_OPEN_FAILED",     -4);    // could not open the 'database'
define("AUTH_ERROR_DB_READ_FAILED",     -5);    // reading the data from the 'database' failed

define('AUTH_ERROR_NIS_BAD_ARGS',      -10);    // the function yp_match didnt return successfully
define('AUTH_ERROR_NIS_CANT_BIND',     -11);    //  can't bind to server on this domain


define("AUTH_FAILED",                   -99);
define("AUTH_EXPIRED",                  -100);


define("AUTH_ERROR_NAME_TAKEN",         -201);
define("AUTH_ERROR_NAME_TOO_SHORT",     -202);
define("AUTH_ERROR_PASSWORD",           -203);


// errors which are "auth_type" dependent use this offset
// to define their errors, so they are unambigious
// drop that
define("AUTH_MEMORY_ERROR_OFFSET",      -1000);


/**
*   this class does common user stuff, such as login, logout, etc.
*
*   @todo some explaination here
*
*   @example    http://wolfram.kriesing.de/programming
*
*   actually i simply wanted to save the auth-object in the session, so the user
*   authentication is retreived every time a page is loaded, the problems with that only are
*   -   we might waste a lot of session 'space', like for DB-objects if they become properties, ie in the Auth_DB-implementation
*   -   we would need some external code which saves the object in the session, i dont know how to handle this
*       inside the class, but i was also not looking for a solution since i dropped the idea of putting the
*       entire object in the session
*
*   @package    Auth
*
*/
class Auth
{

    /**
    *   Parse a data source name, extended the DB::parseDSN functionality
    *   to parse parameters in front and after too urls like: "DB:mysql://root@localhost/test"
    *
    *   adaption of Stig Bakken's DB::connect method, thanks for the idea
    *
    *   @param $dsn string Data Source Name to be parsed
    *   @return array an associative array with the following keys:
    */
    function setup( $type , $dsn , $options=false)
    {
        // just for backward compatibiliy, remove it one day
        // this is not really a secure test :-(
        if( is_array($dsn) && func_num_args()==2 && strpos($type,'://')===false )
            return $this->_oldSetup( $type , $dsn );

        if( !@include_once("Auth/${type}.php") )
            return new Auth_Error( AUTH_ERROR );

        $classname = "Auth_${type}";
// ??? shall we also check if Auth_common is the super-class ???
        if (!class_exists($classname)) {
            return PEAR::raiseError(null, AUTH_ERROR_NOT_FOUND,
                                    null, null, null, 'Auth_Error', true);
        }

        @$obj =& new $classname( $dsn );

        if (is_array($options)) {
            foreach ($options as $option => $value) {
                $test = $obj->setOption($option, $value);
                if (Auth::isError($test)) {
                    return $test;
                }
            }
        } else {
            //$obj->setOption('persistent', $options);
        }

        $err = $obj->setup( $dsn );
// FIXXME check the next line, if only PEAR::isError check isnt enough
        if( Pear::isError($err) || Auth::isError($err) )
        {
            $err->addUserInfo($dsn);
            return $err;
        }

        return $obj;
    }

    /**
    *   Parse a data source name, extended the DB::parseDSN functionality
    *   to parse parameters in front and after too urls like: "DB:mysql://root@localhost/test"
    *
    *   adaption of Stig Bakken's DB::connect method, thanks for the idea
    *
    *   @param $dsn string Data Source Name to be parsed
    *   @return array an associative array with the following keys:
    */
    function _oldSetup( $dsn , $options = false)
    {
        if (is_array($dsn)) {
            $dsninfo = $dsn;
        } else {
            $dsninfo = Auth::parseDSN($dsn);
        }

        // set default authtype to DB if not given
        if( !$dsninfo['authtype'] )
            $dsninfo['authtype'] = 'DB';

        $type = $dsninfo["authtype"];

        // check if $type is a path to a class which is customized
        // that means its not in the (PEAR)-directory or in the include_path
        // i.e. if type is a path to a class, like: /myDir/classes/myCustomeDB
        // this one definetly has to be a child of Auth_common
        $includePath = 'Auth/'; // default
        // a standard class (as in [pear/]Auth/) NEVER starts with a '/'
        if( strpos($type,'/')!==false )
        {
            $includePath = substr($type,0,strrpos($type,'/')+1);
            $type = substr($type,strrpos($type,'/')+1);// set the fileName for type, to be able to check if Auth_fileName is a class
        }

        // make like: MemoryQuest
        if( strpos($type,'DB')!==0 ) // if authtype doesnt start with 'DB' make the class name out of 'authtype.phptype'
        {
            $type = $type.$dsninfo['phptype'];
        }

        $includeFile = $includePath.$type.".php";  // this is default

        if (is_array($options) && isset($options["debug"]) &&
            $options["debug"] >= 2) {
            // expose php errors with sufficient debug level
            include_once $includeFile;
        } else {
            @include_once $includeFile;
        }

        $classname = "Auth_${type}";
// ??? shall we also check if Auth_common is the super-class ???
        if (!class_exists($classname)) {
            return PEAR::raiseError(null, AUTH_ERROR_NOT_FOUND,
                                    null, null, null, 'Auth_Error', true);
        }

        @$obj =& new $classname( $dsninfo );

        if (is_array($options)) {
            foreach ($options as $option => $value) {
                $test = $obj->setOption($option, $value);
                if (Auth::isError($test)) {
                    return $test;
                }
            }
        } else {
            //$obj->setOption('persistent', $options);
        }

        $err = $obj->setup( $dsninfo );
        if(Auth::isError($err))
        {
            $err->addUserInfo($dsn);
            return $err;
        }

        return $obj;
    }

    /**
    *   Parse a data source name, extended the DB::parseDSN functionality
    *   to parse parameters in front and after too urls like: "DB:mysql://root@localhost/test?table=auth"
// this method has moved to myPEAR/Common.php so extend it
// its only still in here since a lot of classes include
// this class simply to use this method
    *
    *   @author Wolfram Kriesing <wolfram@kriesing.de>
    *
    *   @param $dsn string Data Source Name to be parsed
    *   @return array an associative array with the following keys:
    */
    function parseDSN( $dsn )
    {
        $parsed = DB::parseDSN( $dsn );

        if( "test" == $parsed['phptype'] )            // if a url like: "system" is given, !!! improvable !!!
            $parsed['authtype'] = "test";

        // if the [database] name also contains more parameters extract them
        $addParaPos = strpos($parsed['database'],"?");
        if( $addParaPos )
        {
            $temp = explode("&",substr($parsed['database'],$addParaPos+1));     // explode teh parameters
            $parsed['database'] = substr($parsed['database'],0,$addParaPos);    // correct [database] cut the parameters
            foreach( $temp as $aPara )              // save parameters in parameters array
            {
                $tempPara = explode( "=" , $aPara );
                $parsed['parameters'][$tempPara[0]] = urldecode($tempPara[1]);
            }
        }

        // if [phptype] contains a resource before, like: DB:mysql://....  (format adapted from jdbc ... )
        if( sizeof($phpType = explode( ":" , $parsed['phptype'] )) == 2)
        {
            $parsed['authtype'] = $phpType[0];
            $parsed['phptype'] = $parsed['dbsyntax'] = $phpType[1];
        }
//print_r($parsed);
        return $parsed;
    }

    /**
     * Tell whether a result code from an Auth method is an error
     *
     * @param $value int result code
     *
     * @return bool whether $value is an error
     */
    function isError($value)
    {
        return (is_object($value) &&
                (get_class($value) == 'auth_error' ||
                 is_subclass_of($value, 'auth_error')));
    }

    /**
     * Return a textual error message for a DB error code
     *
     * @param $value int error code
     *
     * @return string error message, or false if the error code was
     * not recognized
     */
    function errorMessage($value)
    {
        static $errorMessages;
        if (!isset($errorMessages)) {
            $errorMessages = array(
                AUTH_ERROR                      =>  'unknown error',
                AUTH_ERROR_NOT_FOUND            =>  'not found',
                AUTH_ERROR_DB_CONNECT_FAILED    =>  'connect to "database" failed',
                AUTH_ERROR_DB_OPEN_FAILED       =>  'could not open "database"',
                AUTH_FAILED                     =>  'authentication failed',
                AUTH_ERROR_DB_READ_FAILED       =>  'reading from the "database" failed'
//                AUTH_OK                       => 'no error',
//                AUTH_WARNING                  => 'unknown warning',
                ,AUTH_ERROR_NIS_BAD_ARGS        =>  'NIS: the function yp_match didnt return successfully, most probably the NIS-domain was not found'
                ,AUTH_ERROR_NIS_CANT_BIND       =>  'NIS: cant bind to server on this domain'
            );
        }

        if (Auth::isError($value)) {
            $value = $value->getCode();
        }

        return isset($errorMessages[$value]) ? $errorMessages[$value] : $errorMessages[AUTH_ERROR];
    }


}


/**
 * Auth_Error implements a class for reporting portable database error
 * messages.
 *
 * @package  Auth
 * @author Stig Bakken <ssb@fast.no>
 * @author Wolfram Kriesing <wolfram@kriesing.de>
 */
class Auth_Error extends PEAR_Error
{
    /**
     * Auth_Error constructor.
     *
     * @param $code mixed Auth error code, or string with error message.
     * @param $mode int what "error mode" to operate in
     * @param $level what error level to use for $mode & PEAR_ERROR_TRIGGER
     * @param $debuginfo additional debug info, such as the last query
     *
     * @access public
     *
     * @see PEAR_Error
     */
    function Auth_Error($code = AUTH_ERROR, $mode = PEAR_ERROR_RETURN,
              $level = E_USER_NOTICE, $debuginfo = null)
    {
        if (is_int($code)) {
            $this->PEAR_Error('Auth Error: ' . Auth::errorMessage($code), $code, $mode, $level, $debuginfo);
        } else {
            $this->PEAR_Error("Auth Error: $code", AUTH_ERROR, $mode, $level, $debuginfo);
        }
    }
}


?>
