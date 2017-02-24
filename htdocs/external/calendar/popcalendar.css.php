<?php
    //
    //  $Log: popcalendar.css.php,v $
    //  Revision 1.4  2003/01/28 10:55:44  wk
    //  - simplify code
    //
    //  Revision 1.3  2003/01/13 18:09:38  wk
    //  - use http-caching
    //
    //  Revision 1.2  2003/01/13 12:12:48  cb
    //  - path changed
    //
    //  Revision 1.1  2002/10/28 16:22:55  wk
    //  - initial commit
    //
    //

    require_once 'HTTP/Header/Cache.php';

    // compile the template (if needed), so we can check $tpl->compiled()
    $tpl->compile($layout->getContentTemplate(__FILE__));
    
    $httpCache = new HTTP_Header_Cache();
    $httpCache->setHeader( 'Content-Type' , 'text/css' );
    if( !$tpl->compiled() )
        $httpCache->exitIfCached();

    $httpCache->sendHeaders();

    include($tpl->getCompiledTemplate());

?>
