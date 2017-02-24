<?php
//
//  $Log: Error.php,v $
//  Revision 1.2  2003/03/11 12:57:56  wk
//  *** empty log message ***
//
//  Revision 1.7  2003/01/29 09:59:39  wk
//  - dont use an undefined table name!
//
//  Revision 1.6  2002/11/28 10:28:48  wk
//  - made it possible to add the db-connection later, since it might not be setup at the time when an instance of this object is needed
//
//  Revision 1.5  2002/10/17 14:34:48  wk
//  - whitespaces
//
//  Revision 1.4  2002/07/25 10:08:07  wk
//  - added option idColName
//
//  Revision 1.3  2002/07/08 09:49:17  wk
//  - handle properly if the constructor is called without parameters
//
//  Revision 1.2  2002/07/05 12:05:49  wk
//  - added option saveInDb
//
//  Revision 1.1  2002/06/19 15:24:58  wk
//  - first checkin for vp
//
//

require_once('vp/Page/Message.php');

/**
*   Description
*
*   @package  vp_Page
*   @access   public
*   @version  01/12/10
*   @author   Wolfram Kriesing <wolfram@kriesing.de>
*/
class vp_Page_Error extends vp_Page_Message
{

    var $options = array(
                            'idColName' =>  'id',
                            'columns'   =>  array(
                                                'text'      =>  '$msg',
                                                'timestamp' =>  'time()',
                                                'log'       =>  '$log',     //
                                                'url'       =>  '$_SERVER["PHP_SELF"]',
                                                'query'     =>  '$GLOBALS["QUERY_STRING"]'  //
                                                ),
                            'table'     =>  '',
                            'saveInDb'  =>  true,
                            'verbose'   =>  false
                        );

    function vp_Page_Error( $dbDSN=null , $options=array() )
    {
        parent::vp_Page_Message( $dbDSN , $options );
        if( $dbDSN==null )
        {
            $this->setOption( 'saveInDb' , false );
        }
    }

    function setDbConnection( $dbDSN )
    {                                              
        $this->setOption( 'saveInDb' , true );
        return parent::setDbConnection( $dbDSN );
    }

    /**
    *   saves the error messages in the DB, for debugging
    *
    *   @version    2002/04/26
    *   @access     public
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    */
    function _save( $msg , $log=false )
    {
        if( !$this->getOption('saveInDb') )
            return;
        $data = array();
        foreach( $this->getOption('columns') as $key=>$val )
            eval("\$data[$key] = $val;");
                               
        // dont use the method 'add' to add a new column
        // since an error in there would cause this method to be called recursively

        foreach( $data as $key=>$val )
            $data[$key] = $this->_db->quote($val);

        $table = $this->getOption('table');
        if( $table=='TABLE_ERRORLOG' && !defined($table) )
            die('vp_Page_Error::_save TABLE_ERRORLOG is not defined, or set proper option "table"!');

        $id = $this->_db->nextId( $table );
        $query = sprintf(   'INSERT INTO %s (%s,%s) VALUES (%s,%s)',
                            $table ,
                            $this->getOption('idColName') ,
                            implode(',',array_keys($data)) ,
                            $id , implode(',',$data)
                        );
        if( DB::isError( $res = $this->_db->query($query) ) )
        {
            if( $this->getOption('verbose') )
            {
                echo 'vp_Page_Error (verbose-mode) ERROR : while saving error in DB!<br>';
                print_r($res);
            }
            return false;
        }
    }

    /**
    *   save the message in the db too
    *
    *   @version    2002/04/26
    *   @access     public
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    */
    function log( $msg )
    {
        $this->_save( $msg , true );
        parent::log( $msg );
    }

    /**
    *   save the message in the db too
    *
    *   @version    2002/04/26
    *   @access     public
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    */
    function set( $msg )
    {
        $this->_save( $msg );
        parent::set( $msg );
    }

    /**
    *   this flushes all the errors that currently are in the error-list in the DB
    *   this is needed when the DB-connection is established later, not right when this 
    *   object was instanciated
    *
    */
    function flushData()
    {                 
        foreach( $this->list as $aMsg)
        {
            foreach( $aMsg as $kind=>$aString )
            {                 
                switch( $kind )
                {
                    case 'text':
                                    $this->_save( $aString , false );
                                    break;
                    case 'log':
                                    $this->_save( $aString , true );
                                    break;
                }
            }
        }
    }


} // end of class
?>
