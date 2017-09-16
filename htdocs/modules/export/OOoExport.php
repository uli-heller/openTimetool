<?php
/**
 * 
 * $Id$
 * 
 */

require_once '../../../config.php';

require_once $config->classPath . '/modules/export/export.php';
require_once $config->classPath . '/modules/OOoTemplate/OOoTemplate.php';
require_once $config->classPath . '/modules/project/treeDyn.php';

$isAdmin = $user->isAdmin();
//FIXXME save only templates where the 'save' checkbox was checked,
//needs to be added in the frontend
if (isset($_POST['saveTemplate']) && $_POST['saveTemplate']) {
    if (isset($_POST['export']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
        $_data = array();
        //$_data['data'] = implode('', file($_FILES['file']['tmp_name']));
        // we have to copy the file first, since filesize doesnt work on a tempfile :-(

        $pathInfo = pathinfo($_FILES['file']['name']);
        $_data['type'] = $pathInfo['extension'];
        $extension = $_data['type'];

        @mkdir($config->OOoTemplateDir, 0777);

        $tmpFilename = $config->OOoTemplateDir . '/' . time();
        if (!copy($_FILES['file']['tmp_name'], $tmpFilename)) {
            unset($tmpFilename);
            $applError->set('Could not copy uploaded file, please try again!');
        }

        // only admins are allowed to upload templates
        if ($isAdmin) {
            // unset it here, so the 'if' below wont find a templateId
            // and might use a ambigious templateId
            unset($templateId);
            if ($fp = fopen($tmpFilename, 'rb')) {
                $tmpFileSize = filesize($tmpFilename);
                $_data['data'] = fread($fp, $tmpFileSize);
                fclose($fp);

                // check if the fread succeeded and if the data
                // is at least as much as filesize
                if (strlen($_data['data']) < $tmpFileSize) {
                    // so the 'if' below wont be executed in case this fails
                    unset($tmpFilename);
                    $applError->set('Could not read template file, please try again!');
                } else {
                    $_data['name'] = $_POST['newData']['name'];
                    // only set a templateId if the upload succeeded
                    $templateId = $OOoTemplate->add($_data);
                }
            } else {
                // so the if below wont be executed in case this fails
                unset($tmpFilename);
                $applError->set('Could not open template file, please try again!');
            }
        }
    }
} else {
    // AK : if a template is selected, we'll get the ID here'
    if (isset($_POST['template_id'])) {
        $templateId = (int) $_POST['template_id'];
    }
}

$show = $session->temp->time_index;
// if there are no data for exporting, go back
if (empty($show)) {
    // AK : Doesn't work !
    header('Location: index.php');
    die();
}

$projects = array();
if (!empty($show['projectTree_ids'])) {
    foreach ($show['projectTree_ids'] as $aProjectId) {
        $projects[] = $projectTreeDyn->getPathAsString($aProjectId);
    }
} else {
    $projects = array('all');
}

// AK : get name and mimetype of all templates stored in database !!
// No blob data though
$OOoTemplate->preset();
$OOoTemplate->setOrder('timestamp', true);
if ($templates = $OOoTemplate->getAll(0, 10)) {
    require_once 'vp/Util/MimeType.php';
    foreach ($templates as $key => $_data) {
        $templates[$key]['_type'] = vp_Util_MimeType::getByExtension(
                $_data['type'], 'name') . ' (.' . $_data['type'] . ')';
    }
}

// AK : this is only true when a template has been uploaded and/or selected
if (isset($templateId) || isset($tmpFilename)) {
    if (isset($templateId)) {
        $extension = $OOoTemplate->get($templateId, 'type');
        // AK : getFilename writes the template from DB to
        // template-tmp-dir and returns the path to it
        $templateFilename = $OOoTemplate->getFilename($templateId);
    } else {
        $templateFilename = $tmpFilename;   
    }
    if ($templateFilename) {
        // upload the file into a temp-dir
        $dir = $config->applRoot . '/tmp/OOExport';
        @mkdir($dir, 0777);
        // create a dir-name, try it to be unique
        $uniqueName = time();
        $dir = $dir . '/' . $uniqueName;
        $vDir = $config->vApplRoot . '/tmp/OOExport/' . $uniqueName;
        // upload the file as a unique name
        $file = $dir . '/doc.' . $extension;
        // create a dir for this OO-file to extract it in upload the file in the new dir
        if (mkdir($dir, 0777) && copy($templateFilename, $file)) {
            if (!file_exists($file)) {
                $applError->set('Error copying template file!');
            } else {
                // AK : as an OO-file is in principle a zip-file (see also OOotemplate.php)
                // we can unzip it here to get access to the content.xml where we put our data in	
                // change in the directory and unzip the OO-file
                exec("cd $dir; unzip $file 2>&1 >/dev/null");
                // remove the OO file so it wont be zipped into the new OO file
                unlink($file);

                // put the data in the content.xml file
                // include language or you'll run in troubles as config.php thinks you changed the language
                // as we have to implement an own small session handling (see below) this would lead to
                // a redirect to login-page until we reach the redirection limit  
                $url = $config->vApplRoot . '/' . $_SESSION['lang']
                     . '/modules/export/processOOoFile.php?';
                //$url .= SID;
                $url .= 'template=yes&' . urlencode(session_name()) . '='
                      . urlencode(session_id());
                if (!isset($session->temp->OOoExport)) {
                    $session->temp->OOoExport = new stdClass();
                }
                $session->temp->OOoExport->xmlFile = $dir . '/content.xml';
                //var_dump($_SESSION);

                // that should write the session to disk an end it.
                // in theory and in previous php version the subsequent fopen call
                // worked and had this session in init.php ... But currently !?!?!?! 
                // calling fopen now creates a new empty session, regardless what I'm doing ...
                // => a redirect to login-page until we reach the redirection limit
                session_write_close();

                /**
                 * well now we do our own session handling to overcome
                 * that problem; see init.php !
                 */
                $sessenc = session_encode();
                $tmpsessfile = $config->applRoot . '/tmp/OOExport/' . session_id();
                //echo $tmpsessfile;
                if ($fp = fopen($tmpsessfile, 'wb')) {
                    fwrite($fp, $sessenc);
                    fclose($fp);
                } else {
                    die('Can\'t create temporary session file: ' . $tmpsessfile);
                }

                //var_dump($_SESSION);

                //print "<a href='$url'>go</a><br>$url"; die();
                $options = array(
                    'http' => array(
                        'methode'       => 'GET',
                        'user_agent'    => 'ott',    // who am i
                        'max_redirects' => 10,       // stop after 10 redirects
                        'timeout'       => 120,      // timeout on response
                        //'follow_location' => false,  // no redirects at all
                    )
                );
                $context = stream_context_create($options);

                // AK : We open the processOOofile.php.result as file !!!
                $table = '';
                //if( $fp = fopen( $url , 'rb', false, $context ) )
                //if( $fp = fopen( $url , 'rb' ) )
                if (($table = file_get_contents($url, false, $context)) !== false) {
                    //while( $string = fread($fp,4096) ) $table .= $string;
                    //fclose($fp);

                    if ($fp = fopen($dir . '/content.xml', 'wb')) {
                        fwrite($fp, $table);
                        fclose($fp);

                        // needed since oo 3 (templates); this empty file creates a corrupt OO-report
                        // shouldn't happen again as the unzip cmd above created it unintentionally
                        // by a wrong redirection of stdout and stderr ;-) 
                        @unlink($dir . '/1');

                        // AK : Create new OO document by zipping
                        // zip -r means recursive, -m means delete the packed files
                        exec("cd $dir; zip -rm new.zip *;mv new.zip new.$extension; chmod 777 new.$extension");

                        // copy the file into the exported-directory (tmp/_exportDir)
                        // and write fileinfo to DB
                        $id = $export->saveFile("$dir/new.$extension", $templateId);

                        // AK : Now we clean up all these unique directories and tmp-files
                        if ($id) {
                            @rmdir($dir);
                        }

                        header('Location: index.php?exportedId=' . $id);
                        // die here, to go to the page where the user shall donwload
                        die();
                    }
                }
            }
        }
    }
}

$layout->setMainLayout('/modules/dialog');
require_once $config->finalizePage;
