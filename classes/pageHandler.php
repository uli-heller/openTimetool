<?php
/**
 * 
 * $Id$
 * 
 */

require_once 'vp/Application/HTML/SaveHandler.php';

class modules_pageHandler extends vp_Application_SaveHandler
{

    var $options = array(
        'saveButton'      => 'action_save',
        'saveAsNewButton' => 'action_saveAsNew',
        'primaryCol'      => 'id',
    );

}

$pageHandler = new modules_pageHandler($applError, $applMessage);
