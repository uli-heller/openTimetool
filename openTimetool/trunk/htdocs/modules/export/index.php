<?php
    /**
     * 
     * $Id
     * 
     * This script is called when on time/index.php the export button is pushed.
     * It builds the export page displayed in a popup window 
     * 
     * Some php notices need to be eliminated
     * 
     * ************* switch to svn ********
    *   $Log: index.php,v $
    *   Revision 1.9  2003/02/14 15:39:53  wk
    *   - add csv exports
    * 
    *   Revision 1.8  2002/12/05 14:19:03  wk
    *   - added braces
    * 
    *   Revision 1.7  2002/11/30 18:37:05  wk
    *   - translate 'print'
    * 
    *   Revision 1.6  2002/11/25 10:48:30  wk
    *   - add next-prev logic for exported files
    * 
    *   Revision 1.5  2002/11/20 20:09:54  wk
    *   - let only admins upload templates and show only user's exported files
    * 
    *   Revision 1.4  2002/11/12 15:30:20  wk
    *   - dont show html export, since the file still contains too many references to the website
    * 
    *   Revision 1.3  2002/11/12 13:11:55  wk
    *   - link to proper page
    * 
    *   Revision 1.2  2002/11/11 17:57:44  wk
    *   - index does different thing now!
    * 
    *   Revision 1.1  2002/08/05 18:52:26  wk
    *   - initial commit
    * 
    */


	// as we dont have auto_prepend anymore, we have to include our config here
	require_once("../../../config.php");

	require_once($config->classPath.'/modules/export/export.php');
    require_once('vp/Application/HTML/NextPrev.php');
    

    $exports = array(   'HTML'  =>          array(
                                                array(  'type'=>t('print'),
                                                        'file'=>'printView.php'
                                                    )
                                                ,array( 'type'=>'PDF',
                                                        'file'=>'printView.php'
                                                    )
                                                ,array( 'type'=>'csv (Excel)',
                                                        'file'=>'printView.php?action_toCsvXsl=1'
                                                    )
                                                ,array( 'type'=>'csv (OpenOffice.org)',
                                                        'file'=>'printView.php?action_toCsvOOo=1'
                                                    )
/* dont offer HTML, since we need to remove the css, favicon, title, etc. but not yet ...
                                                ,array( 'type'=>'HTML',
                                                        'file'=>'printView'
                                                    )
*/
                                            ),
                        'OpenOffice.org' => array(
                                                array(  'type'=>'OpenOffice.org',
                                                        'file'=>'OOoExport.php'   // AK : added .php ...
                                                    )
                                                ,array( 'type'=>'Word .doc',
                                                        'todo'=>true)
                                                ,array( 'type'=>'PDF',
                                                        'todo'=>true)
                                            )
                        );
    
    $export->preset();

    if (isset($_REQUEST['removeId'])) {
    	if($_REQUEST['removeId'] == 'all') {
    		$export->deleteAllFiles();
    	} else {
    	  	$export->deleteFile($_REQUEST['removeId']);
    	}
    }
    
 

    $nextPrev = new vp_Application_HTML_NextPrev($export);
    $nextPrev->setLanguage( $lang );
    
    $exportedFiles = $nextPrev->getData();
    if( $exportedFiles )
    {
        /* set _type like:  OpenOffice.org - Writer (.sxw) instead of only sxw */
        require_once('vp/Util/MimeType.php');
        foreach( $exportedFiles as $key=>$data ) {
            $exportedFiles[$key]['_type'] = vp_Util_MimeType::getByExtension($data['type'],'name').' (.'.$data['type'].')';
        }

        $ids = array();
        foreach( $exportedFiles as $aFile ) {
            $ids[] = $aFile['id'];
        }
//        $projects = $exported2project->getMultiple( $ids );
    }
   
    if(isset($_REQUEST['exportedID']) && $_REQUEST['exportedID'] == '-1') {
    	// AK : tried export with nothing to export ...See printView.php
    	$applError->set('No data for export ...'); 
    }



    $layout->setMainLayout('/modules/dialog');
    require_once($config->finalizePage);
    
?>
