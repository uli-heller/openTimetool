<?php
    //
    //  $Log: style.css.php,v $
    //  Revision 1.5  2003/01/13 18:13:38  wk
    //  - use http-caching
    //
    //  Revision 1.4  2002/10/28 16:24:37  wk
    //  - use $styleSheet -class vars
    //
    //  Revision 1.3  2002/10/24 14:15:59  wk
    //  - moved definitions to php file
    //  - added vp...-class
    //
    //  Revision 1.2  2002/08/29 13:32:24  wk
    //  - set proper content-type
    //
    //  Revision 1.1.1.1  2002/07/22 09:37:37  wk
    //
    //
    //
                      
    require_once 'HTTP/Header/Cache.php';

    // compile the template (if needed), so we can check $tpl->compiled()
    $tpl->compile($layout->getCssTemplate());

    $httpCache = new HTTP_Header_Cache();
    $httpCache->setHeader( 'Content-Type' , 'text/css' );
    $httpCache->exitIfCached( !$tpl->compiled() );

    $httpCache->sendHeaders();

    include($tpl->getCompiledTemplate());

?>
