<?php
    //
    //  $Log: OOoExport.php,v $
    //  Revision 1.10.2.1  2003/04/02 11:06:34  wk
    //  - retreive the proper extension from the uploaded file, IE sent mime-type 'application/x-zip-compressed' for sxw files :-( so we use the extension now
    //
    //  Revision 1.10  2003/03/04 19:13:39  wk
    //  - use treeDyn
    //
    //  Revision 1.9  2003/02/17 19:16:49  wk
    //  - use the session if we have it :-)
    //
    //  Revision 1.8  2003/01/06 11:32:43  wk
    //  - updated comment
    //
    //  Revision 1.7  2003/01/06 11:26:47  wk
    //  - check the file reading
    //
    //  Revision 1.6  2003/01/06 11:09:00  wk
    //  - some additional error handling
    //
    //  Revision 1.5  2002/11/20 20:09:54  wk
    //  - let only admins upload templates and show only user's exported files
    //
    //  Revision 1.4  2002/11/12 18:00:27  wk
    //  - forgot to mkdir
    //
    //  Revision 1.3  2002/11/12 15:29:30  wk
    //  - go to index page for download
    //
    //  Revision 1.2  2002/11/12 13:10:58  wk
    //  - show export info
    //
    //  Revision 1.1  2002/11/11 17:56:56  wk
    //  - initial commit
    //
    //


    require_once($config->classPath.'/modules/export/export.php');
    require_once($config->classPath.'/modules/OOoTemplate/OOoTemplate.php');
    require_once($config->classPath.'/modules/project/treeDyn.php');
                      
    $isAdmin = $user->isAdmin();
//FIXXME save only templates where the 'save' checkbox was checked, needs to be added in the frontend
    if( $_POST['saveTemplate'] )
    {
        if( $_POST['export'] &&
            is_uploaded_file($_FILES['file']['tmp_name']) )
        {
            $_data = array();
//            $_data['data'] = implode('',file( $_FILES['file']['tmp_name'] ));
            // we have to copy the file first, since filesize doesnt work on a tempfile :-(
    
            $pathInfo = pathinfo($_FILES['file']['name']); 
            $_data['type'] = $pathInfo['extension'];
            $extension = $_data['type'];


            @mkdir($config->OOoTemplateDir,0777);

            $tmpFilename = $config->OOoTemplateDir.'/'.time();
            if( !copy($_FILES['file']['tmp_name'],$tmpFilename) )
            {                                     
                unset($tmpFilename);
                $applError->set('Could not copy uploaded file, please try again!');
            }

            // only admins are allowed to upload templates
            if( $isAdmin )
            {
                unset($templateId); // unset it here, so the 'if' below wont find a templateId and might use a ambigious templateId
                if( $fp = fopen($tmpFilename,'rb') )
                {                                 
                    $tmpFileSize = filesize($tmpFilename);
                    $_data['data'] = fread( $fp , $tmpFileSize );
                    fclose($fp);                              
                        
                    if( strlen($_data['data']) < $tmpFileSize ) // check if the fread succeeded and if the data is at least as much as filesize
                    {
                        unset($tmpFilename);    // so the 'if' below wont be executed in case this fails
                        $applError->set('Could not read template file, please try again!');
                    }
                    else
                    {
                        $_data['name'] = $_POST['newData']['name'];
                        $templateId = $OOoTemplate->add($_data);    // only set a templateId if the upload succeeded
                    }
                }
                else
                {
                    unset($tmpFilename);    // so the if below wont be executed in case this fails
                    $applError->set('Could not open template file, please try again!');
                }
            }
        }
    }
    else
    {
        if( $_POST['template_id'] )
            $templateId = $_POST['template_id'];
    }                           
                 
    $show = $session->temp->time_index;
    if( !$show )    // if there are no data for exporting, go back
    {
        header('Location: index.php');
        die();
    }

    $projects = array();
    if( sizeof($show['projectTree_ids']) )
    {          
        foreach( $show['projectTree_ids'] as $aProjectId )
            $projects[] = $projectTreeDyn->getPathAsString($aProjectId);
    }
    else
    {
        $projects = array('all');
    }

    $OOoTemplate->preset();
    $OOoTemplate->setOrder('timestamp',true);
    if( $templates = $OOoTemplate->getAll(0,10) )
    {
        require_once('vp/Util/MimeType.php');
        foreach( $templates as $key=>$_data )
            $templates[$key]['_type'] = vp_Util_MimeType::getByExtension($_data['type'],'name').' (.'.$_data['type'].')';
    }



    if( $templateId || $tmpFilename )
    {

        if( $templateId ) {
            $extension = $OOoTemplate->get($templateId,'type');
            $templateFilename = $OOoTemplate->getFilename($templateId);
        } else {
            $templateFilename = $tmpFilename;   
        }

        if( $templateFilename )
        {
            // upload the file into a temp-dir
            $dir = $config->applRoot.'/tmp/OOExport';
            @mkdir( $dir , 0777 );
            $uniqueName = time();                       // create a dir-name, try it to be unique
            $dir = $dir.'/'.$uniqueName;
            $vDir = $config->vApplRoot.'/tmp/OOExport/'.$uniqueName;
            $file = $dir.'/doc.'.$extension;            // upload the file as a unique name
            if( mkdir( $dir , 0777 ) &&                 // create a dir for this OO-file to extract it in
                copy( $templateFilename , $file ) )     // upload the file in the new dir
            {
                if (!file_exists($file)) {
                    $applError->set('Error copying template file!');
                } else {
                    exec("cd $dir; unzip $file &2>1");      // change in the directory and unzip the OO-file
                    unlink($file);                          // remove the OO file so it wont be zipped into the new OO file

                    // put the data in the content.xml file
                    $url = $config->vApplRoot.'/modules/export/processOOoFile.php?';
                    $url.= '&'.urlencode(session_name()).'='.urlencode(session_id());
                    $session->temp->OOoExport->xmlFile = $dir.'/content.xml';
                    session_write_close();
//print "<a href='$url'>go</a><br>$url";die();

                    if( $fp = fopen( $url , 'rb' ) )
                    {
                        while( $string = fread($fp,4096) )
                            $table .= $string;
                        fclose($fp);

                        if( $fp = fopen( $dir.'/content.xml' , 'w' ) )
                        {
                            fwrite($fp,$table);
                            fclose($fp);

                            exec("cd $dir; zip -rm new.zip *;mv new.zip new.$extension; chmod 777 new.$extension"); // zip -r means recursive, -m means delete the packed files

                            $id = $export->saveFile( "$dir/new.$extension" , $templateId );
                            header('Location: index.php?exportedId='.$id);
                            die();  // die here, to go to the page where the user shall donwload
                        }
                    }
                }
            }
        }
    }

    $layout->setMainLayout('/modules/dialog');
    require_once($config->finalizePage);


?>
