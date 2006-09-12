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
// | Authors: Ronny Moreas <ronny.moreas@mech.kuleuven.ac.be>             |
// +----------------------------------------------------------------------+
//
// $Log: NIS.php,v $
// Revision 1.3  2003/03/11 12:57:56  wk
// *** empty log message ***
//
// Revision 1.3  2002/08/20 19:16:55  mccain
// - added check of another error case
//
// Revision 1.2  2002/08/20 14:32:33  mccain
// - check error codes
//
// Revision 1.1  2002/06/19 21:12:10  mccain
// - new container classes, thanks to Ronny Moreas <ronny.moreas@mech.kuleuven.ac.be>
//

require_once("Auth/common.php");

define("AUTH_MEMORY_ERROR_FILE_NOT_FOUND",  AUTH_MEMORY_ERROR_OFFSET-1);

/**
*
*
*   @package    Auth
*/

class Auth_NIS extends Auth_common
{

    /**
    *
    *
    *   @var    string  domain
    */
    var $domain;

    /**
    *   maps the entry number to an identifier
    *   the entry number means at which position in the file what is written
    *   like: "0:1:2:3" maps to "username:password:role:group"
    *
    *   @var    string  map
    */
    var $map = array(   0=>'username',
                        2=>'uid',
                        3=>'gid',
                        4=>'gecko' );

    /**
    *
    *   create NIS authentication driver for the given domain
    *
    *   @access public
    *   @author Ronny Moreas <ronny.moreas@mech.kuleuven.ac.be>
    *   @param  string  $domain
    */
    function Auth_NIS( $domain )
    {
        $this->Auth_common();
        $this->domain = $domain;
    }

    /**
    *   try to authenticate the user, comparing username and password
    *   with the given source, does the work for login
    *
    *   @author Ronny Moreas <ronny.moreas@mech.kuleuven.ac.be>
    *   @param  string  $username
    *   @param  string  $password
    *   @param  boolean true on success
    */
    function _login( $username , $password )
    {
        $ret = array();
        $authenticated = false;
        $yp_line = @yp_match($this->domain,'passwd.byname',$username);

        switch( yp_errno() )
        {
            case 1: // could be that the nis-domain could not be connected
                    return $this->raiseError(AUTH_ERROR_NIS_BAD_ARGS);
            case 3: // can't bind to server on this domain
                    return $this->raiseError(AUTH_ERROR_NIS_CANT_BIND);
            case 5: // the given key doesnt exists, the username is not registered on the nis-server
                    return AUTH_FAILED;
        }

        if ($yp_line)
        {
            $yp_data = explode(':',$yp_line);

            if (crypt($password,$yp_data[1]) == $yp_data[1])
            {
                // OK, we have a match, fill the data array
                foreach( $yp_data as $key => $value )
                {
                    if( isset($this->map[$key]) )
                        $ret[$this->map[$key]] = $value;
                }
                $gecko = explode(',',$ret['gecko']);
                $ret['fullname'] = $gecko[0];
                $authenticated = true;
            }
        }
        if( !$authenticated )
            return AUTH_FAILED;

        return $ret;
    }
}
?>