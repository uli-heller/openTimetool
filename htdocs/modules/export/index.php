<?php
/**
 * 
 * $Id$
 * 
 */

require_once '../../../config.php';

require_once $config->classPath.'/modules/export/export.php';
require_once 'vp/Application/HTML/NextPrev.php';

$exports = array(
    'HTML' => array(
        array(
            'type' => t('print'),
            'file' => 'printView.php',
        ),
        array(
            'type' => 'PDF',
            'file' => 'printView.php',
        ),
        array(
            'type' => 'csv (Excel)',
            'file' => 'printView.php?action_toCsvXsl=1',
        ),
        array(
            'type' => 'csv (OpenOffice.org)',
            'file' => 'printView.php?action_toCsvOOo=1',
        ),
/* dont offer HTML, since we need to remove the css, favicon, title, etc. but not yet ...
        array(
            'type' => 'HTML',
            'file' => 'printView',
        ),
*/
    ),
    'OpenOffice.org' => array(
        array(
            'type' => 'OpenOffice.org',
            'file' => 'OOoExport.php',
        ),
        array(
            'type' => 'Word .doc',
            'todo' => true,
        ),
        array(
            'type' => 'PDF',
            'todo' => true,
        ),
    ),
);

$export->preset();

if (isset($_REQUEST['removeId'])) {
    if ($_REQUEST['removeId'] == 'all') {
        $export->deleteAllFiles();
    } else {
        $export->deleteFile($_REQUEST['removeId']);
    }
}

$nextPrev = new vp_Application_HTML_NextPrev($export);
$nextPrev->setLanguage($lang);

$exportedFiles = $nextPrev->getData();
if ($exportedFiles) {
    /* set _type like:  OpenOffice.org - Writer (.sxw) instead of only sxw */
    require_once 'vp/Util/MimeType.php';
    foreach ($exportedFiles as $key => $data) {
        $exportedFiles[$key]['_type'] = vp_Util_MimeType::getByExtension(
                $data['type'], 'name') . ' (.' . $data['type'] . ')';
    }

    $ids = array();
    foreach ($exportedFiles as $aFile) {
        $ids[] = $aFile['id'];
    }
    //$projects = $exported2project->getMultiple($ids);
}

if (isset($_REQUEST['exportedID']) && $_REQUEST['exportedID'] == '-1') {
    // AK : tried export with nothing to export ...See printView.php
    $applError->set('No data for export ...');
}

$layout->setMainLayout('/modules/dialog');
require_once $config->finalizePage;
