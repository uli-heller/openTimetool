<?php
//
//  $Log: remote.php,v $
//  Revision 1.3  2003/01/28 10:53:50  wk
//  - optional port
//
//  Revision 1.2  2002/11/28 10:30:17  wk
//  - use the settings from the config
//
//  Revision 1.1  2002/11/26 15:58:43  wk
//  - initial commit
//
//       

require_once('XML/RPC.php');

/**
*   contains commonly used utility functions, which are needed in this project
*
*   @package  proxy
*   @access   public
*   @author   Wolfram Kriesing <wolfram@kriesing.de>
*   @version  2002/03/05
*/
class modules_remote
{

    /**
    *
    *
    *   @access     public
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @version    2002/03/05
    */
    function execute( $methodName )
    {                        
        global $applError,$config;

        $paras = func_get_args();
        array_shift($paras);
        if (is_array($paras) && sizeof($paras)>0) {
            $vals = array();
            foreach( $paras as $aPara )
            {
                $vals[] = XML_RPC_encode($aPara);
            }
            $msg =    new XML_RPC_Message($methodName,$vals);
        } else {
            $msg =    new XML_RPC_Message($methodName,$vals);
        }
                                                                                              
        $port = isset($config->backOffice->port) ? $config->backOffice->port : 80;
        $client = new XML_RPC_Client( $config->backOffice->path , $config->backOffice->host , $port );

        if( $config->backOffice->authUser && $config->backOffice->authPassword )
            $client->setCredentials( $config->backOffice->authUser , $config->backOffice->authPassword );

//$client->debug=1;

        $errorMessage = 'Retreiving your Account data failed, please try loging again! '.
                        'If it won\'t work again, please contact your support contact!';

        $p = $client->send($msg);
        if( PEAR::isError($p) )
        {
            //print_r($p);
            //print($p->getMessage());
            $applError->log($p->getMessage());
            $applError->set( $errorMessage );
            return false;
        }

        if( $p->faultCode() )
        {
            $applError->log($p->faultString());
            $applError->set( $errorMessage );
            return false;
        }

        $res = $p->value();
        $data = XML_RPC_decode($res);
//                print "got by xml-rpc:"; print_r($data);

        return $data;
    }

    /**
    *
    *
    *   @access     public
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @version    2002/03/05
    */
/*
    function getType( $val )
    {
        $type = gettype($val);
        switch( $type )
        {
            case 'integer':  return 'int';
            default:        return $type;
        }
    }
*/    

} // end of class

?>
