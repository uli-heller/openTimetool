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
*   $Log: IMAP.php,v $
*   Revision 1.3  2003/03/11 12:57:56  wk
*   *** empty log message ***
*
*   Revision 1.1  2002/08/20 19:15:56  mccain
*   - inital commit
*
*
*/

require_once("Auth/common.php");

/**
*   this class authenticates against the system users
*   please watch that this module also returns false if the server couldnt be
*   connected, that's just the way the php-imap functions work
*
*   example calls are.
*   $auth = Auth::setup( 'IMAP' , 'pop3/notls://host:110' , $options );
*   $auth = Auth::setup( 'IMAP' , 'pop3://host:110' , $options );
*
*   @package    Auth
*/

class Auth_IMAP extends Auth_common
{

    /**
    *
    *   @access public
    *   @version    2002/08/19
    *   @author Wolfram Kriesing <wolfram@kriesing.de>
    */
    function Auth_IMAP( $dsninfo )
    {
        $this->Auth_common();
        $parsed = DB::parseDSN($dsninfo);
        $this->_imapService = $parsed['phptype'];
        $this->_imapPort = $parsed['port'];
        $this->_imapHost = $parsed['hostspec'];
        if( !$this->_imapPort )
        {
            switch( strtolower($this->_imapService) )
            {
                case 'pop3':    $this->_imapPort = 110;
                                break;
                case 'imap':    $this->_imapPort = 143;
                                break;
            }
        }
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
        $ret = AUTH_FAILED;

        if( $mbox = @imap_open ('{'.$this->_imapHost.':'.$this->_imapPort.'/'.$this->_imapService.'}INBOX',
                                $username , $password ) )
        {
            imap_close($mbox);
            $ret = true;
        }
/*        else
        {
            $ret = $this->raiseError( implode(',',imap_errors()) );
        }
*/

        return $ret;
    }
}
?>