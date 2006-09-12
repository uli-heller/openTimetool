<?php
/**
 * $Id
 * 
 * 
 * ********* switch to SVN ********
 *  $Log: common.php,v $
 *  Revision 1.7  2003/02/10 16:00:12  wk
 *  - add table to tableSpec
 *
 *  Revision 1.6  2003/01/28 10:54:07  wk
 *  - use db_querytool
 *
 *  Revision 1.5  2002/12/10 18:10:45  wk
 *  - add tables to tableSpec
 *
 *  Revision 1.4  2002/11/21 19:14:59  wk
 *  - turn verbose mode off in live-mode
 *
 *  Revision 1.3  2002/11/11 17:55:52  wk
 *  - added tables to tableSpec
 *
 *  Revision 1.2  2002/10/28 11:18:27  wk
 *  - extend the vp_DB_Relation now
 *
 *  Revision 1.1.1.1  2002/07/22 09:37:37  wk
 *
 *
*/

require_once 'DB/QueryTool.php';

/**
*   this class should be extended
*   it does commonly needed stuff, such as:
*   - providing commonly used DB-methods
*
*   @package
*   @version    2002/04/02
*   @access     public
*   @author     Wolfram Kriesing <wolfram@kriesing.de>
*/
class modules_common extends DB_QueryTool
{

    var $primaryCol = 'id';

    var $tableSpec = array(
                                array('name'    =>  TABLE_USER,             'shortName' =>  'user')
                                ,array('name'   =>  TABLE_PROJECTTREE2USER, 'shortName' =>  'projectTree2user')
                                ,array('name'   =>  TABLE_OOOTEMPLATE,      'shortName' =>  'OOoTemplate')
                                ,array('name'   =>  TABLE_EXPORTED,         'shortName' =>  'exported')
                                ,array('name'   =>  TABLE_TIME,             'shortName' =>  'time')
                                ,array('name'   =>  TABLE_TASK,             'shortName' =>  'task')
                                ,array('name'   =>  TABLE_PROJECTTREE,      'shortName' =>  'projectTree')
                        );

    /**
    *   construct the object and pass the proper values
    *
    *   @version    2002/04/12
    *   @access     public
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    */
    function modules_common( $table=null )
    {
        global $db, $applError, $config;

        if($table != null)
            $this->table = $table;

        // dont write so many error logs :-) in a live env
        // this will not write error logs when i.e. trimming a col, or when removing a col which doesnt exist in the db
        if( $config->isLiveMode() )
            $this->setOption('verbose',false);

        parent::DB_QueryTool_Query( $config->dbDSN );
       
        $this->setErrorSetCallback( array(&$applError,'set') );
        $this->setErrorLogCallback( array(&$applError,'log') );
    }

}   // end of class
?>
