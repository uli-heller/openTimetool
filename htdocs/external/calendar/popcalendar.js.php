<?php
    //
    //  $Log: popcalendar.js.php,v $
    //  Revision 1.8  2003/01/13 18:09:38  wk
    //  - use http-caching
    //
    //  Revision 1.7  2003/01/13 12:12:59  cb
    //  - path changed
    //
    //  Revision 1.6  2002/11/29 16:54:37  wk
    //  - use I18N
    //
    //  Revision 1.5  2002/10/24 14:10:37  wk
    //  - put only the php in here and cache the result :-)
    //
    //

    require_once 'HTTP/Header/Cache.php';
    $httpCache = new HTTP_Header_Cache();
    $httpCache->setHeader( 'Content-Type' , 'text/javascript' );

    $thisTplFile = $layout->getContentTemplate();
    $tpl->setOption('cacheFileExtension','js');

    $isTplCached = $tpl->isCached($thisTplFile);

    // if template is cached, which also means it was not
    // compiled again, so the user-agent has the same as the one we have cached
    // then we can send a HTTP-'304 Not Modified' and exit here
    $httpCache->exitIfCached( $isTplCached );
    // the call above sets headers too, so call sendHeaders here!
    $httpCache->sendHeaders();

    // since we cache the resulting file we dont need to do this in here
    // its not much but its some, and since the browser caches the js-file too
    // it might even have a minor effect, but anyway
    if( !$isTplCached )
    {
        // get the month names for the current locale
        $monthNames = '"'.implode('","',$dateTime->getMonthNames()).'"';

//FIXXXXME this is not very international!!!!
        $dayNames = $dateTime->getDayNames(true);
        $sunday = array_shift($dayNames);
        array_push($dayNames,$sunday);
        $dayNames   = '"'.implode('","',$dayNames).'"';
    }

    // we need to set the content type, since gemini would parse this file
    // and fail badly, i know this has to be fixed in gemini ... and i am doing that too
    $tpl->compile($thisTplFile);
    include($tpl->getCompiledTemplate());
?>
