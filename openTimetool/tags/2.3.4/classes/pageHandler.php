<?php
//
//  $Log: pageHandler.php,v $
//  Revision 1.1.1.1  2002/07/22 09:37:37  wk
//
//
//


require_once('vp/Application/HTML/SaveHandler.php');


class modules_pageHandler extends vp_Application_SaveHandler
{

    var $options = array(   'saveButton'        =>  'action_save',
                            'saveAsNewButton'   =>  'action_saveAsNew',  //
                            'primaryCol'        =>  'id'    //
                            );

}

$pageHandler = new modules_pageHandler( $applError , $applMessage );

?>