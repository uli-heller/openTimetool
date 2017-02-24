<?php
	/**
    *
    *	$Id
    *
    *	Another very central piece of code which will be run through after any 
    *	page call. It in principle includes all the required pieces of php-files and compiled
    *	templates (which are php-files as well) and builts the final real page the browser gets !
    *  
    *
    *********** switch to SVN *************
    *  $Log: finalize.php,v $
    *  Revision 1.5  2003/03/10 19:28:05  wk
    *  - just some debugging stuff
    *
    *  Revision 1.4  2003/03/04 19:19:42  wk
    *  - just some debug info
    *
    *  Revision 1.3  2002/11/11 18:03:51  wk
    *  - use config->isLiveMode
    *  - some new config-stuff
    *
    *  Revision 1.2  2002/08/29 13:36:06  wk
    *  - benchmarking
    *
    *  Revision 1.1.1.1  2002/07/22 09:37:37  wk
    *
    *
    *  Revision 1.1.1.1  2002/06/20 11:09:44  wk
    *
    *
    *
	*/

   if( !$config->isLiveMode() ) $processingTimer->setMarker('content done (finalize.php start)');

    // here we are first processing all php-files
    // so we can catch errors etc. from each and they all will be shown
    // and then we compile all the templates and include the main-tpl
    // which includes all the compiled templates :-)

    // read out the main template and find the templates that really get _included_
//print 'main tpl = '.$layout->getMainTemplate()."<br>\n";
//AK    $mainTpl = implode('',file( $layout->getMainTemplate() , 'r' ));
//AK 	  file parameter have changed ... 2nd param is obsolete ...
    $mainTpl = implode('',file( $layout->getMainTemplate()));    
    preg_match_all('/.*include\(\$.*->get(.*)Template/',$mainTpl,$_finalizePages);

	/**
    * process all php-files, that belong to the templates
    * that actually shall be shown
    */ 
    foreach( $_finalizePages[1] as $aFile )
    {
        if( $aFile == 'Content' )
            continue;
        $method = "get$aFile"."File";
        if( $_file = $layout->$method() )
        {
        	// include all pieces one after the other	
            include($_file);
        }
    }

    if( !$config->isLiveMode() ) $processingTimer->setMarker('finalize.php get*File done');

	/**
    * compile each of the templates
    */ 
    foreach( $_finalizePages[1] as $aFile )
    {
        $method = "get$aFile"."Template";
        if( $_file = $layout->$method() )
        {
            $tpl->compile($_file);
            $method = "setCompiled$aFile"."Template";
            $layout->$method($tpl->compiledTemplate);
        }
    }

//echo "finalize nearly done<br>"; 
//var_dump($layout);
//echo "<br>This way layout content<p>";
    if( !$config->isLiveMode() ) $processingTimer->setMarker('finalize.php done');

    // compile the main template itself
    include($layout->getMainFile());
  
    $tpl->compile($layout->getMainTemplate());
    
    // and include the compiled main template
    include($tpl->compiledTemplate);


/* // auth debugging    
print "userAuth->_session['timestamp']=".$dateTime->format($userAuth->_session['timestamp']);
print "<br>userAuth->options['expire']=".$dateTime->formatTime($userAuth->options['expire'])." ({$userAuth->options['expire']})";
print "<br>userAuth->expired=".($userAuth->expired?'true':'false');

print "<br>timestamp+expire=".($userAuth->_session['timestamp'] + $userAuth->options['expire']);
print "<br>time()=".time();
print "<br>diff=".(time()-$userAuth->_session['timestamp'] + $userAuth->options['expire']);
*/
/*
if ($config->runMode=='develop') {
    print '<font style="color:white;font-size:20px;">';    
    if (in_array('/usr/local/httpd/vhosts/ashley-timetool/classes/modules/project/tree.php',get_required_files())) {
        print 'projectTree REQUIRED';
        if (strtolower(get_class($projectTree))=='modules_project_tree') {
            print "<br>instanciated";
        } else {
            print "<br>NOT instanciated";
        }
    }
    print '</font>';
}
*/
/*foreach($GLOBALS['_queryAnalyze'] as $key=>$aQuery) {
    print "<br>$key ... ";print_r($aQuery);print '<br>';
}
*/
?>
