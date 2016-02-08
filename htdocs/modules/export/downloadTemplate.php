<?php
    //
    //  $Log: downloadTemplate.php,v $
    //  Revision 1.1  2002/11/11 17:56:56  wk
    //  - initial commit
    //
    //


	// as we dont have auto_prepend anymore, we have to include our config here
	require_once("../../../config.php");

	require_once($config->classPath.'/modules/OOoTemplate/OOoTemplate.php');

    if( !$_REQUEST['id'] )
    {
        header('Location: index.php');
        die();
    }

    $OOoTemplate->putFile( $_REQUEST['id'] );

?>
