<?php
//
//  $Log: XMLRPC.php,v $
//  Revision 1.1  2003/03/11 12:58:09  wk
//  *** empty log message ***
//
//  Revision 1.2  2002/12/12 15:40:52  wk
//  - use debug option and faultString
//
//  Revision 1.1  2002/12/10 15:26:12  wk
//  - initial commit
//
//

require_once 'PEAR.php';
require_once 'XML/RPC.php';
require_once 'vp/WebService/Methods/Common.php';

/**
*   contains commonly used utility functions, which are needed in this project
*
*   @package  proxy
*   @access   public
*   @author   Wolfram Kriesing <wolfram@kriesing.de>
*   @version  2002/03/05
*/
class vp_WebService_Methods_XMLRPC extends vp_WebService_Methods_Common
{                                 

    /**
    *
    *
    *   @access     public
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @version    2002/12/10
    */
    function execute( $methodName )
    {
        $this->_error = null;
        $this->_isError = true;

        $paras = func_get_args();
        array_shift($paras);
        if( is_array($paras) && sizeof($paras)>0 )
        {
            $vals = array();
            foreach( $paras as $aPara )
            {
                $vals[] = XML_RPC_encode($aPara);
            }
            $msg =    new XML_RPC_Message($methodName,$vals);
        }
        else
            $msg =    new XML_RPC_Message($methodName,$vals);
                                                                 
        $client = new XML_RPC_Client( $this->_file , $this->_host );

        if( $this->_needsAuth )
            $client->setCredentials( $this->_username , $this->_password );
            
        if( $this->getOption('debug') )
            $client->debug=1;

        $p = $client->send($msg);
        if( PEAR::isError($p) )
        {
            $this->_error = $p;
            return false;
        }

        if( $p->faultCode() )
        {
            $this->_error = $p->faultString();
            return false;
        }

        $res = $p->value();
        $this->_data = XML_RPC_decode($res);

        $this->_isError = false;

        return true;
    }
} // end of class

?>
