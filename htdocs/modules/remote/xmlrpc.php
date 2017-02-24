<?php
    //
    //  $Log: xmlrpc.php,v $
    //  Revision 1.4.2.1  2003/04/10 18:05:36  wk
    //  - use references for getInstance!
    //
    //  Revision 1.4  2003/03/04 19:15:50  wk
    //  - get projectTree by getInstance()
    //
    //  Revision 1.3  2003/01/30 16:12:54  wk
    //  - added time.getFiltered
    //
    //  Revision 1.2  2003/01/30 14:45:13  wk
    //  - add project.getAllAvailable
    //
    //  Revision 1.1  2002/12/13 12:15:07  wk
    //  - initial commit
    //
    //

    require_once $config->classPath.'/modules/time/time.php';
    require_once $config->classPath.'/modules/project/tree.php';

    require 'XML/RPC/Server.php';


    /**
    *
    *   @param  string  the account name
    *   @return mixed   either an array with all the data or false if accountName was not found
    *                   or no data were found for the account name
    */
    function getProject( $passedParas )
    {
        global $time;

        $projectId = XML_RPC_decode($passedParas->getParam(0));

        $time->reset();
        $time->autoJoin( TABLE_TASK );

        if( $projectId )
            $time->setWhere( 'projectTree_id='.$projectId );
        $time->addWhere( TABLE_TASK.'.calcTime=1' );
        $time->setOrder( 'timestamp' , true );
                 
            
        $times = $time->getAll();
        if( $times )
        foreach( $times as $k=>$aTime )
            foreach( $aTime as $key=>$val )
                $times[$k][$key] = utf8_encode(htmlspecialchars($val));

        return XML_RPC_encode( $times );
    }

    function project_getAllAvailable()
    {
        $projectTree =& modules_project_tree::getInstance(true);
        $_projects = $projectTree->getAllAvailable();
        $projects = array();
        if( $_projects )
        foreach ($projects as $k=>$aProject) {
            foreach ($aProject as $key=>$val) {
                if (!is_array($val)) {
                    $projects[$k][$key] = utf8_encode(htmlspecialchars($val));
                }
            }
        }

        return XML_RPC_encode( $times );
    }

    function time_getFiltered( $passedParas )
    {
        global $time;

        $filter = XML_RPC_decode($passedParas->getParam(0));

        $times = $time->getFiltered($filter,true);
        if( $times )
        foreach( $times as $k=>$aTime )
            foreach( $aTime as $key=>$val )
                $times[$k][$key] = utf8_encode(htmlspecialchars($val));

        return XML_RPC_encode( $times );
    }


    $methods = array(
                        'project.get'=>array(
                                        'function'=>'getProject',
                                        //'signature'=>array(array($GLOBALS['XML_RPC_String'],$GLOBALS['XML_RPC_String'])),
                                        'docstring'=>''
                                        ),
                        'project.getAllAvailable'=>array(
                                        'function'=>'project_getAllAvailable',
                                        //'signature'=>array(array($GLOBALS['XML_RPC_String'],$GLOBALS['XML_RPC_String'])),
                                        'docstring'=>''
                                        ),
                        'time.getFiltered'=>array(
                                        'function'=>'time_getFiltered',
                                        //'signature'=>array(array($GLOBALS['XML_RPC_String'],$GLOBALS['XML_RPC_String'])),
                                        'docstring'=>''
                                        )
/*                        ,'account.isActive'=>array(
                                        'function'=>'isAccountActive',
                                        //'signature'=>array(array($GLOBALS['XML_RPC_String'],$GLOBALS['XML_RPC_String'])),
                                        'docstring'=>''
                                        )
*/
                    );

    $server = new XML_RPC_Server($methods);

?>
