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
*   $Log: DB.php,v $
*   Revision 1.3  2003/03/11 12:57:56  wk
*   *** empty log message ***
*
*   Revision 1.3  2002/03/18 22:00:30  mccain
*   - do properly digest the password
*
*   Revision 1.2  2002/02/07 22:06:46  mccain
*   - added informational comment
*
*   Revision 1.1.1.1  2002/02/07 17:26:33  mccain
*
*   ##### those are my local revisions, from before moving it to sourceforge :-) #####
*   ##### just kept for informational reasons, might be removed one day
*
*   Revision 1.11  2002/02/07 05:30:29  cain
*   - use PEAR::parseDSN instead of custom
*
*   Revision 1.10  2002/01/21 23:01:53  cain
*   - added license statement
*
*   Revision 1.9  2001/11/11 17:34:49  cain
*   - dont need to require DB.php, since Auth.php does that
*
*   Revision 1.8  2001/10/31 00:19:30  cain
*   - forgot the very important 'return parent::setup'
*   - moved the parameters copy to common
*
*   Revision 1.7  2001/10/30 21:54:52  cain
*   - decided to call the method which finally accesses the data source '_login' (before it was authenticate) so the association is easier
*
*   Revision 1.6  2001/10/29 23:11:55  cain
*   - added package name
*
*   Revision 1.5  2001/10/29 19:20:39  cain
*   - renamed files and includes to be PEAR-compatible (without 'Auth_' before)
*
*   Revision 1.4  2001/10/27 16:43:52  cain
*   - implemented first working DB-Auth
*
*   Revision 1.3  2001/10/24 17:24:35  cain
*   - require Auth_common
*
*   Revision 1.2  2001/10/17 21:03:26  cain
*   - added logging of cvs messages
*
*
*/

require_once("Auth/common.php");

/**
*
*
*   @package    Auth
*/

class Auth_DB extends Auth_common
{

    /**
    *
    *   @access public
    *   @author Wolfram Kriesing <wolfram@kriesing.de>
    */
    function Auth_DB( $dsninfo )
    {
        $this->Auth_common();

        // add the additional options which can be set for this class
        // i dont know another way to 'extend' properties .. is it even possible?
        $this->options['table']     =   'auth';
        $this->options['usernameColumn']    =   'username';
        $this->options['passwordColumn']    =   'password';
    }

    /**
    *
    *
    *   @access public
    *   @author Wolfram Kriesing <wolfram@kriesing.de>
    */
    function setup( $dsninfo )
    {
        // setup DB connection
        if( DB::isError($db=DB::connect( $dsninfo )) )
            return $this->raiseError(AUTH_ERROR_DB_CONNECT_FAILED, null, null,
                                        null, DB::errorMessage($db) );
        $this->dbh = &$db;
        $this->dbh->setFetchMode( DB_FETCHMODE_ASSOC );
//echo "AK AuthDB Setup = <br>";var_dump($this->dbh);echo "<br>";

        return parent::setup( $this->dbh->dsn );
    }

    /**
    *   try to authenticate the user, comparing username and password
    *   with the given source, does the work for login
    *
    *   @author Wolfram Kriesing <wolfram@kriesing.de>
    *   @param  string  $username
    *   @param  string  $password
    *   @param  boolean true on success
    */
    function _login( $username , $password )
    {
//print("authenticate with $username , $password");
        $query = sprintf("SELECT * FROM %s WHERE %s=%s AND %s=%s",
                            $this->options['table'],
                            $this->options['usernameColumn'],$this->dbh->quote($username),
                            $this->options['passwordColumn'],$this->dbh->quote($this->digest($username,$password))
                         );
//echo "includes/Auth/DB : ";print_r($query);
        if( DB::isError( $res = $this->dbh->getRow($query)) )
        {     		
            return $this->raiseError(AUTH_ERROR_DB_READ_FAILED, null, null,
                                        null, DB::errorMessage($res) );
        }

        unset($res[$this->options['passwordColumn']]);  // erase the password from the data, so it wont be visible in the session data
        if( sizeof($res) )                          // if the query returns data return them back
            return $res;

        return AUTH_FAILED;                         // no data found
    }
}
?>