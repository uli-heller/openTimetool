<?php
    //
    //  $Log: adminMode.php,v $
    //  Revision 1.1  2002/11/13 19:02:08  wk
    //  - show when switching to admin mode
    //
    //      
    // AK : added @ to avoid notice           
    if( @$_REQUEST['adminModeOn'] )
        $user->adminModeOn();
    else                          
        $user->adminModeOff();
        
    $isAdmin = $user->isAdmin();

    require_once($config->finalizePage);

?>