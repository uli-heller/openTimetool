<?php
//
//  $Log: Common.php,v $
//  Revision 1.1  2003/03/11 12:58:09  wk
//  *** empty log message ***
//
//  Revision 1.2  2002/12/12 15:40:32  wk
//  - use options
//
//  Revision 1.1  2002/12/10 15:26:12  wk
//  - initial commit
//
//

require_once 'XML/RPC.php';
require_once 'vp/OptionsDB.php';


/**
*   contains commonly used utility functions, which are needed in this project
*
*   @package  proxy
*   @access   public
*   @author   Wolfram Kriesing <wolfram@kriesing.de>
*   @version  2002/03/05
*/
class vp_WebService_Methods_Common extends vp_OptionsDB
{

    /**
    *   @var
    */
    var $_error = null;

    /**
    *   @var    boolean 
    */
    var $_isError = false;



    /**
    *   @var    string  the host name where to execute the remote method
    */
    var $_host = null;

    /**
    *   @var    string  the host name where to execute the remote method
    */
    var $_file = null;

    /**
    *   @var    string
    */
    var $_username =  null;

    /**
    *   @var    string
    */
    var $_password =  null;

    /**
    *   @var    boolean true if the execution needs authentication
    */
    var $_needsAuth = false;


    var $options    =   array(
                                'debug' => false
                            );

    function vp_WebService_Methods_Common( $dsn )
    {
        $this->_dsn = $dsn;

        // [phptype] => XMLRPC [dbsyntax] => XMLRPC [username] => user [password] => password
        // [protocol] => tcp [hostspec] => ashley.unix.vp [port] => [socket] => [database] => path/to/file )

        $this->_host =      $this->_dsn['hostspec'];
        $this->_file =      '/'.$this->_dsn['database'];

        $this->_username =  $this->_dsn['username'];
        $this->_password =  $this->_dsn['password'];
        $this->_needsAuth = $this->_username ? true : false;
    }

    /**
    *
    *
    *   @access     public
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @version    2002/12/10
    */
    function getData()
    {
        return $this->_data;
    }

    /**
    *
    *
    *   @access     public
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @version    2002/12/10
    */
    function isError()
    {
        return $this->_isError;
    }

    /**
    *
    *
    *   @access     public
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @version    2002/12/10
    */
    function getError()
    {
        return $this->_error;
    }

}

?>
