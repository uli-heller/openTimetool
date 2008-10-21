<?php
   /**
    *  $Id
    * 
    * Lot of php noctices stuff
    * 
    * Is called when exporting to pdf or csv ...
    * 
    * ********** switch to SVN ******	 
    *  $Log: printView.php,v $
    *  Revision 1.7  2003/02/14 15:40:48  wk
    *  - add csv export
    *
    *  Revision 1.6  2003/01/29 10:11:23  wk
    *  - switch to new CS
    *
    *  Revision 1.5  2003/01/13 18:12:20  wk
    *  - sort the dates properly
    *  - use Xipe,
    *  - set additional tpl-vars
    *
    *  Revision 1.4  2002/12/05 14:19:36  wk
    *  - remove the pageHeader for the exported file
    *
    *  Revision 1.3  2002/11/30 18:37:30  wk
    *  - fix order of times
    *  - some formatting
    *
    *  Revision 1.2  2002/11/12 15:30:38  wk
    *  - go to index page for download
    *
    *  Revision 1.1  2002/11/12 13:10:24  wk
    *  - initial commit
    *
    */

    require_once($config->classPath.'/modules/project/treeDyn.php');
    require_once($config->classPath.'/modules/project/member.php');
    require_once($config->classPath.'/modules/time/time.php');
    require_once($config->classPath.'/modules/export/export.php');

	// AK : use GPL php class for that job ...
	require_once($config->applRoot.'/html2pdf/HTML_ToPDF.php');

    $show = $session->temp->time_index;

//print_r($show);echo " = time_index<br>";
//print_r($_REQUEST);echo " = request<br>";

// copy from time/index.php!!!!
    // set the authenticated user's id if none was chosen in the frontend
    $isManager = $projectMember->isManager();
    if( !$show['user_ids'] || !$isManager )
    {
        $show['user_ids'] = array();    // empty the array and show only this users data!!!
        $show['user_ids'][0] = $userAuth->getData('id');
    }

    $time->preset();
    if( isset($show['comment']) )
        $time->addWhereSearch( TABLE_TIME.'.comment' , $show['comment'] );
    if( isset($show['projectTree_ids']) )
        $time->addWhere(TABLE_PROJECTTREE.'.id IN('.implode(',',$show['projectTree_ids']).')');
    if( isset($show['task_ids']) )	// AK : instead of sizeof
        $time->addWhere(TABLE_TASK.'.id IN('.implode(',',$show['task_ids']).')');
    if( isset($show['user_ids']) )
        $time->addWhere(TABLE_USER.'.id IN('.implode(',',$show['user_ids']).')');
    $curUserId = $userAuth->getData('id');
    $isAdmin = $user->isAdmin();
                                                                     
    if( $_times = $time->getDay($show['dateFrom'],$show['dateUntil']) )
    {
        $_lastDate = 0;
        $times = array();
        foreach( $_times as $aTime )
        {
            $_date = date('dmY',$aTime['timestamp']);
            if( $_date != $_lastDate && isset($aDayTimes))  // AK : isset instead of sizeof
            {
                $times = array_merge(array_reverse($aDayTimes),$times);
                $aDayTimes = array();
            }
            // set an additional field, which we can check in the template to know if the current user
            // can edit this entry
            $aTime['_canEdit'] = $isAdmin || $curUserId == $aTime['_user_id'];
            $aDayTimes[] = $aTime;
            $_lastDate = $_date;
        }
        $times = array_merge(array_reverse($aDayTimes),$times); // add the last day too :-)
    }

// copy end





    $showCols['task'] = ( !isset($_REQUEST['cols']) || (isset($_REQUEST['cols']) && isset($_REQUEST['cols']['task'])) );
    $showCols['start'] = ( !isset($_REQUEST['cols']) || (isset($_REQUEST['cols']) && isset($_REQUEST['cols']['start'])) );
    $showCols['duration'] = ( !isset($_REQUEST['cols']) || (isset($_REQUEST['cols']) && isset($_REQUEST['cols']['duration'])) );
    $showCols['comment'] = ( !isset($_REQUEST['cols']) || (isset($_REQUEST['cols']) && isset($_REQUEST['cols']['comment'])) );
    $showCols['project'] = ( !isset($_REQUEST['cols']) || (isset($_REQUEST['cols']) && isset($_REQUEST['cols']['project'])) );
    if( sizeof(@$show['projectTree_ids'])==1 )   // AK : added @
        $showCols['project'] = false;

    if( isset($_REQUEST['showAllColumns']) )
        $showCols = array('task'=>true,'start'=>true,'duration'=>true,'comment'=>true,'project'=>true);


    $numCols = 0;
    foreach( $showCols as $aCol )
        if( $aCol )
            $numCols++;


	if(empty($times)) {
	    // AK : show the export page again if there is nothing to export  
    	// Is better than an empty page as before ...
	    $applError->set('No data for export ...');  // has to be set in index.php ...
    	header('Location: index.php?exportedID=-1');
	    die();
	}  

    $exportIt = false;
    if( isset($_REQUEST['action_toHTML']) || isset($_REQUEST['action_toPDF']) )
    {
        ob_start();
    }
    if( isset($_REQUEST['action_toHTML']) || isset($_REQUEST['action_toPDF']) || isset($_REQUEST['action_print']) )
    {
        $pageProp->set('modules/export/printView',false);   // dont show no pageHeader in the exported file
        $exportIt = true;
    }

    if (!isset($_REQUEST['action_toCsvOOo']) && !isset($_REQUEST['action_toCsvXsl'])) {
         $layout->setMainLayout('/modules/dialog');
        require_once($config->finalizePage);
    }

    //
    //  PDF and print
    //
    if (isset($_REQUEST['action_toHTML']) || isset($_REQUEST['action_toPDF'])) {
    
        $content = ob_get_contents();
        $filenamePrefix = $config->exportDir.'/'.md5(time().session_id());
        $filename = $filenamePrefix.'.html';

//print_r($filename);echo " = filename<br>";


        if( !@is_dir($config->exportDir) ) {
            if(!mkdir($config->exportDir,0777)) {
            	// AK : I don't believe that this error would ever come up ...
            	// Who is displaying the applError in this popup-window ?
                //$applError->set('Could not make directory \''.$config->exportDir.'\'!');
                //return false;
                // AK : we use echo and die
                echo ('Could not make directory \''.$config->exportDir.'\'!');
                die();
            }
        }

        if ($fp = fopen( $filename , 'w' )) {
            fwrite( $fp , $content );
            fclose( $fp );
        }

        if (isset($_REQUEST['action_toHTML'])) {
            ob_end_flush();
            $export->saveFile( $filename , 0 );
        } elseif (isset($_REQUEST['action_toPDF'])) {
            $pdfFilename = $filenamePrefix.'.pdf';
            
   			$tmpcmd = trim(@$config->html2pdf);
   			if(!empty($tmpcmd)) {
 	           $html2pdfCmd = str_replace( '$1' , $filename , $config->html2pdf );
    	       $html2pdfCmd = str_replace( '$2' , $pdfFilename , $html2pdfCmd );
   			}
			else {
				$tmpcmd = '';
			}

			if(!empty($tmpcmd)) {
				// AK : added another check -> maybe wrong config parameter ...
				// Just die afterwards. We are in a popup-window and that can be tolerated !
				$tmpc = explode(' ',$config->html2pdf);
 	        	if( !@is_file($tmpc[0]) ) {
 	        		ob_end_flush();
	                echo ('HTML2PDF-Converter not found : \''.$config->html2pdf.'\'! Check your config.php !');
		            die();
	    	    }
			}
			
			// silently delete html-output-buffer
            ob_end_clean();
            
            if(empty($tmpcmd)) {
            	// AK : use the php class for conversion
				$pdf =& new HTML_ToPDF($filename, $config->vApplRoot, $pdfFilename);
            	if(isset($config->$html2psPath)) $pdf->setHtml2Ps($config->$html2psPath);
            	if(isset($config->$ps2pdfPath)) $pdf->setPs2Pdf($config->$ps2pdfPath);            					   
				// Set headers/footers
				$pdf->setHeader('color', 'blue');
				$pdf->setFooter('left', $config->applName);
				$pdf->setFooter('right', '$D');
				$result = $pdf->convert();

				// Check if the result was an error
				if (PEAR::isError($result)) {
    				die($result->getMessage());
				}
            }
            else {
            	// AK : we use the external program configured in config.php (original code)
            	exec($html2pdfCmd.' &2>1');
            }              
            
            if (is_file($pdfFilename)) {
                $id = $export->saveFile( $pdfFilename );
                header('Location: index.php?exportedId='.$id);
                die();
            }
        }
    }
    
    //
    //  CSV export
    //
    if (isset($_REQUEST['action_toCsvOOo']) || isset($_REQUEST['action_toCsvXsl'])) {        
            if (isset($_REQUEST['action_toCsvXsl'])) {
                $extension = '.xls.csv';
            } else {  
                $extension = '.ods.csv';   // AK : use the OO2 suffix from now on. 
            }

            $filenamePrefix = $config->exportDir.'/'.md5(time().session_id());
            $filename = $filenamePrefix.$extension;

			$seperator = trim($config->seperator);
			if(empty($seperator)) $seperator = ';'; 
 //print_r($times);           
            ob_start();
            $keys = array();
            foreach ($times[0] as $key=>$x) {
                if ($key=keyValue($key)) {                	
                	// sx : well I think I'll never understand that encoding stuff completely :-( ......
                	$tmp = html_entity_decode($key,ENT_NOQUOTES, 'ISO8859-1');  // we have html encoded strings in the german language file
                    $keys[] = iconv(iconv_get_encoding('input_encoding'),'ISO-8859-1',$tmp);;  // we have html encoded strings in the german language file
                }
            }
            print implode($seperator,$keys)."\r\n";

            foreach ($times as $aTime) {
                $data = array();
                foreach ($aTime as $key=>$oneEntry) {
                    if (($oneEntry=keyValue($key,$oneEntry))!==false) {
                        	$data[] = $oneEntry;                   	
                    }
                }
                print implode($seperator,$data)."\r\n";
            }
            $content = ob_get_contents();        
            ob_end_clean();            
//die();            
            if ($fp = fopen( $filename , 'w' )) {
                fwrite( $fp , $content );
                fclose( $fp );
            }
            $id = $export->saveFile($filename);
            header('Location: index.php?exportedId='.$id);
            die();            
    }
    
    /**
    *   this function checks if and how the key shall be used in the CSV file
    *   if the key shall have a different name it renames it here, if the value
    *   shalll be changed too, it does that here too
    */
    function keyValue($key,$val=null)
    {
        global $projectTreeDyn,$dateTime,$time,$config;
    
        $mapkeys = array(   '_projectTree_id'   =>  'project'
                            ,'_user_name'       =>  'name'
                            ,'_user_surname'    =>  'surname'
                            ,'_task_name'       =>  'task'
                            ,'timestamp'        =>  '"date";"time"'  // SX :very tricky 4 spliting in 2 columns
                            ,'durationSec'        =>  'duration'  //SX : we are now using durationSec instead if duration -> better 4 calculation in Excel
                        );
        $dropKeys = array(  '_projectTree_','_user_','_task_','_canEdit','id',
                            'user_id','projectTree_id','task_id','duration'  //SX : see remark above
                        );              
                        
		$seperator = trim($config->seperator);
		if(empty($seperator)) $seperator = ';';                                   
        
        if (isset($mapkeys[$key]) && $mapkeys[$key]===false) {
            return false;
        }
        if (!isset($mapkeys[$key])) {
            foreach ($dropKeys as $aDrop) {
                if (strpos($key,$aDrop)===0) {
                    return false;
                }
            }
        }
                        
        if ($val!==null) {
            $ret = $val;
            switch ($key) {
                case '_projectTree_id':
                    $ret = $projectTreeDyn->getPathAsString($val);     
                    break;
                case 'timestamp':
                	// sx: we split the date-time-string in 2 columns now ...
					$ret = array();                	
                    //$ret = $dateTime->format($val);
                    $ret[] = $dateTime->formatDate($val);     
                    $ret[] = $dateTime->formatTime($val);                              
                    break;
                case 'durationSec':
                	$ret = $time->_calcDuration($val,'decimal');  // sx: using durationSec now -> get the decimal calculation as hours
                	break;
            }
        } else {
            if (isset($mapkeys[$key])) {
                $ret = $mapkeys[$key];
            } else {
                $ret = $key;
            }
            $ret = t($ret); // translate the headline
            return $ret;
        }
        
        if(is_array($ret)) {
        	$rets = '';
        	foreach($ret as $r) {
        		if(!empty($rets)) $rets .= $seperator;
        		$rets .= _cleanfield($r);
        	}
        	$ret = $rets;
        } else {
        	$ret = _cleanfield($ret);
        }
        
        return $ret;
    }
    
function _cleanfield($ret,$simple=false) {
       // remove line feeds, by spaces
        $ret = str_replace("\r\n"," ",$ret);
        // replace " by double ""
        if(!$simple) {
        	$ret = str_replace('"','""',$ret);
        	$ret = "\"$ret\"";
        }
        
        return $ret;
}    

?>
