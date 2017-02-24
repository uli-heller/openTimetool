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
*   $Log: Memory.php,v $
*   Revision 1.3  2003/03/11 12:57:56  wk
*   *** empty log message ***
*
*   Revision 1.5  2002/05/24 15:04:55  mccain
*   - fixed bug which lets u log in without password and username !!!
*   - added comments
*
*   Revision 1.4  2002/03/18 22:01:04  mccain
*   - do properly digest the password
*
*   Revision 1.3  2002/02/26 15:18:06  mccain
*   - renamed database to filename
*
*   Revision 1.2  2002/02/07 22:06:46  mccain
*   - added informational comment
*
*   Revision 1.1.1.1  2002/02/07 17:26:33  mccain
*
*   ##### those are my local revisions, from before moving it to sourceforge :-) #####
*   ##### just kept for informational reasons, might be removed one day
*
*   Revision 1.10  2002/01/21 23:01:53  cain
*   - added license statement
*
*   Revision 1.9  2001/10/30 21:54:52  cain
*   - decided to call the method which finally accesses the data source '_login' (before it was authenticate) so the association is easier
*
*   Revision 1.8  2001/10/29 23:11:55  cain
*   - added package name
*
*   Revision 1.7  2001/10/29 19:20:39  cain
*   - renamed files and includes to be PEAR-compatible (without 'Auth_' before)
*
*   Revision 1.6  2001/10/27 16:44:19  cain
*   - use proper error handling
*
*   Revision 1.5  2001/10/26 11:57:30  cain
*   - some modularizing to extend the class easier, like MemoryQuest does
*
*   Revision 1.4  2001/10/25 18:27:17  cain
*   - added map to map the file entries
*
*   Revision 1.3  2001/10/24 17:24:53  cain
*   - require Auth_common
*   - ignore comment lines in the passwd file
*
*   Revision 1.2  2001/10/17 21:04:24  cain
*   - first version which works with auto auth
*
*
*/

require_once("Auth/common.php");

define("AUTH_MEMORY_ERROR_FILE_NOT_FOUND",  AUTH_MEMORY_ERROR_OFFSET-1);

/**
*
*
*   @package    Auth
*/

class Auth_Memory extends Auth_common
{

    /**
    *
    *
    *   @var    string  filename
    */
    var $filename;

    /**
    *   maps the entry number to an identifier
    *   the entry number means at which position in the file what is written
    *   like: "0:1:2:3" maps to "username:password:role:group"
    *
    *   @var    string  map
    */
    var $map = array(   0=>'username',
                        1=>'password',
                        2=>'role',
                        3=>'group' );

    /**
    *
    *   @access public
    *   @author Wolfram Kriesing <wolfram@kriesing.de>
    */
    function Auth_Memory( $file )
    {
        $this->Auth_common();

        $this->filename = $file;
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
        $authenticated = false;
        if( Auth::isError($err = $this->openFile()) )
            return $err;

        // read from the given file
        while( $data = $this->readLine() )
        {
            if( $username==$data['username'] && $this->digest($username,$password)==trim($data['password']) )
            {
                $authenticated = true;
                break;
            }
        }
        $this->closeFile();

        if( !$authenticated )
            return AUTH_FAILED;

        unset($data['password']);                   // dont let the password be visible in the session data
        return $data;
    }

    /**
    *   open the password file
    *
    *   @author Wolfram Kriesing <wolfram@kriesing.de>
    *   @return     mixed   either true on success or an error
    */
    function openFile()
    {
        // we can check if file exists, if not raise DB_NOT_FOUND error ...

        if( $this->fp = @fopen( $this->filename , "r" ) )
            return true;
        return $this->raiseError(AUTH_ERROR_DB_OPEN_FAILED);
    }

    /**
    *   close the password file
    *
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    */
    function closeFile()
    {
        fclose( $this->fp );
    }

    /**
    *   read a line from the password file
    *
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @return     mixed   either false on failure or the data in an array
    */
    function readLine()
    {
        while( $aLine = fgets($this->fp,1000) )
        {
            $aLine = trim($aLine);
            if( !$aLine || $aLine[0] == '#' )       // drop empty and comment lines
                continue;

            $curData = explode( ":" , $aLine );
            foreach( $curData as $key=>$aDataField )
            {
                if( isset($this->map[$key]) )
                    $ret[$this->map[$key]] = $aDataField;
            }
            return $ret;
        }
        return false;
    }

}
?>