<?php
   /**
    *  $Id
    * 
    * ******** switch to svn ****** 
    *  $Log: download.php,v $
    *  Revision 1.3  2003/02/14 15:40:12  wk
    *  - CS issues
    *
    *  Revision 1.2  2002/12/05 14:18:49  wk
    *  - pass download para
    *
    *  Revision 1.1  2002/11/11 17:56:56  wk
    *  - initial commit
    *
    */

    require_once($config->classPath.'/modules/export/export.php');
                      
    if( !isset($_REQUEST['id']) ) {							// AK : isset
        require_once 'HTTP/Header.php';
        HTTP_Header::redirect('index.php');
    }

    $export->putFile($_REQUEST['id'],@$_REQUEST['download']);  // AK : @

?>
