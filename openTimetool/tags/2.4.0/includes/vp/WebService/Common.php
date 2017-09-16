<?php
//
//  $Log: Common.php,v $
//  Revision 1.1  2003/03/11 12:58:09  wk
//  *** empty log message ***
//
//  Revision 1.2  2002/12/12 15:41:06  wk
//  - require PEAR
//
//  Revision 1.1  2002/12/10 15:26:12  wk
//  - initial commit
//
//

require_once 'DB.php';
require_once 'PEAR.php';

/**
*   contains commonly used utility functions, which are needed in this project
*
*   @package  proxy
*   @access   public
*   @author   Wolfram Kriesing <wolfram@kriesing.de>
*   @version  2002/03/05
*/
class vp_WebService_Common
{

    /**
    *
    *
    *   @access     public
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @version    2002/12/10
    */
    function setup( $dsn , $options=array() )
    {
        $parsedDsn = DB::parseDSN( $dsn );
        // [phptype] => XMLRPC [dbsyntax] => XMLRPC [username] => user [password] => password
        // [protocol] => tcp [hostspec] => ashley.unix.vp [port] => [socket] => [database] => path/to/file )
        
        $type = $parsedDsn['phptype'];

        if( !include_once("vp/WebService/Methods/${type}.php") )
            return new PEAR_Error('unknown vp/WebService/Method : '.$parsedDsn['phptype']);

        $classname = "vp_WebService_Methods_${type}";
// ??? shall we also check if Auth_common is the super-class ???
        if (!class_exists($classname)) {
            return PEAR::raiseError(null, AUTH_ERROR_NOT_FOUND,
                                    null, null, null, 'Auth_Error', true);
        }

        @$obj = new $classname( $parsedDsn );

        if( !is_object($obj) )
            return new PEAR_Error('could not make instance of '.$classname);

        if (is_array($options) && sizeof($options) )
        {
            foreach ($options as $option => $value) {
                $test = $obj->setOption($option, $value);
                if (PEAR::isError($test)) {
                    return $test;
                }
            }
        }

        return $obj;
    }

} // end of class

?>
