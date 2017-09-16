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
*   $Log: XMLRPC.php,v $
*   Revision 1.3  2003/03/11 12:57:56  wk
*   *** empty log message ***
*
*   Revision 1.3  2002/03/18 22:02:18  mccain
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
*   Revision 1.5  2002/01/21 23:01:53  cain
*   - added license statement
*
*   Revision 1.4  2001/10/31 21:03:18  cain
*   - now the login method from the XMLRPC-server can also return an array and it is handled properly
*   - added additional XMLRPC-options
*
*   Revision 1.3  2001/10/31 00:20:52  cain
*   - first implementation of XMLRPC authentication ... and it works :-)
*
*   Revision 1.2  2001/10/29 23:11:55  cain
*   - added package name
*
*   Revision 1.1  2001/10/29 22:48:59  cain
*   *** empty log message ***
*
*
*/

require_once("Auth/common.php");
require_once("XML_RPC/RPC.php");    // i know this should be XML/RPC.php but i didnt install pear proerply yet :-(

/**
*
*
*   @package    Auth
*/

class Auth_XMLRPC extends Auth_common
{

    /**
    *   the XMLRPC-client object i use to connect to call the remote-method
    *
    *   @var    Object  client
    */
    var $client;

    /**
    *
    *   @access public
    *   @author Wolfram Kriesing <wolfram@kriesing.de>
    */
    function Auth_XMLRPC( $dsninfo )
    {
        $this->Auth_common();

        // add the additional options which can be set for this class
        // i dont know another way to 'extend' properties .. is it even possible?

        // this is the remote mathod that shall be called to authenticate,
        // the default is not of big use i guess, but anyway
        $this->options['loginMethod']     =   'Auth.login';

        // connection data for the XMLRPC server
        $this->options['serverPort']     =   '80';
        // data for Authentication-realm at the XMLRPC server, to get in the server at all
        $this->options['serverAuthUsername']     =   '';
        $this->options['serverAuthPassword']     =   '';
    }

    /**
    *
    *
    *   @access public
    *   @author Wolfram Kriesing <wolfram@kriesing.de>
    */
    function setup( $dsninfo )
    {
        // is there a port given in the hostspec? we need to have it seperate
        $port = 80;
        if( strpos($dsninfo['hostspec'],':') )
        {
            $hostspec = explode(':',$dsninfo['hostspec']);
            $dsninfo['hostspec'] = $hostspec[0];
            $port = $hostspec[1];
        }

        // create the xml-rpc-client object
        $this->client = new XML_RPC_Client( $dsninfo['database'] , $dsninfo['hostspec'] , $port );

        if( $dsninfo['username'] )
            $this->setOption('serverAuthUsername',$dsninfo['username']);
        if( $dsninfo['password'] )
            $this->setOption('serverAuthPassword',$dsninfo['password']);

        return parent::setup( $dsninfo );
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
        $vals[] = new XML_RPC_Value( $username ,'string');
        $vals[] = new XML_RPC_Value( $this->digest($username,$password) ,'string');

        $msg =    new XML_RPC_Message($this->options['loginMethod'],$vals);

        // are any authentication data for authorizing at the XMLRPC-server given?
        if( $this->options['serverAuthUsername'] )
            $this->client->setCredentials( $this->options['serverAuthUsername'] , $this->options['serverAuthPassword'] );

        $res = $this->client->send($msg);           // send the remote message
        if( PEAR::isError($res) )
            return $this->raiseError( $res->getMessage() );

        // something like 'Unknown method' or whatever the XML-RPC-server says
        if( $res->faultCode() )
            return $this->raiseError( $res->faultString() );

        // now $res should be an XML_RPC_Response-object ... finally
        // TODO better check the object type too ... or?
        $res = $res->value();                       // get the XML_RPC_Value
        $res = XML_RPC_decode($res);                // decode the value, since it still contains XML_RPC_Value-objects

        if( is_string( $res ) )
            return $this->raiseError( $res );

        // a _login might return additional data too :-)
        if( is_array( $res ) )
            return $res;

#        if( $res === true ) cant compare this way yet, since XMLRPC doesnt do 'settype'
# at this moment $res is still an 'integer', even though the XMLRPC server returned a boolean
        if( $res == true )
            return AUTH_OK;

        return AUTH_FAILED;                         // no data found
    }
}
?>