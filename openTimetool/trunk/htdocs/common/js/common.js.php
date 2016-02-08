<?php
    // 
    //  $Log: common.js.php,v $
    //  Revision 1.5  2003/01/13 18:10:11  wk
    //  - use http-caching
    //
    //  Revision 1.4  2002/12/01 15:09:30  wk
    //  - started with the caching stuff
    //
    //  Revision 1.3  2002/11/30 18:36:34  wk
    //  - remove unnecessary code
    //
    //  Revision 1.2  2002/11/29 16:54:20  wk
    //  - set proper content type
    //
    //  Revision 1.1  2002/11/29 15:08:41  wk
    //  - init commit
    //
    //       

	// as we dont have auto_prepend anymore, we have to include our config here
	require_once("../../../config.php");


    require_once 'HTTP/Header/Cache.php';
    $httpCache = new HTTP_Header_Cache();
    $httpCache->setHeader( 'Content-Type' , 'text/javascript' );

    $tplFile = $layout->getContentTemplate(__FILE__);
    $tpl->compile( $tplFile );

    $httpCache->exitIfCached( !$tpl->compiled() );
    // the call above sets headers too, so call sendHeaders here!
    $httpCache->sendHeaders();

    include($tpl->getCompiledTemplate());

?>
