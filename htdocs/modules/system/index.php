<?php
/**
 * 
 * $Id$
 * 
 */

require_once '../../../config.php';

$isAdmin = $user->isAdmin();

if ($isAdmin) {
    $mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : '';
    $opcache = ($config->opcache &&
            extension_loaded('Zend OPcache') && !empty(ini_get('opcache.enable')));

    switch ($mode) {
        case 'tmpdir':
        case 'opcache':
            $output = '';
            if (isset($_POST['id']) && isset($session->temp->adminUniqueId) &&
                    $_POST['id'] === $session->temp->adminUniqueId) {
                unset($id);
                unset($session->temp->adminUniqueId);
                if ($mode == 'tmpdir' && !$config->demoMode) {
                    $res = $util->recRemDir($config->tmpDir, $config->tmpDir);
                    $output = (empty($res)) ? 'none.' : implode("\n", $res);
                } else if ($mode == 'opcache' && $opcache) {
                    $output = 'reset php opcache ... '
                            . (@opcache_reset() ? 'ok' : 'error');
                }
            }
            if (empty($output)) {
                $id = md5(microtime(true));
                $session->temp->adminUniqueId = $id;
            }
            break;

        case 'phpinfo':
            if ($config->phpInfo) {
                phpinfo();
                die();
            }
            break;

        default:
            $mode = '';
    }
} else {
    $applMessage->setOnce('Available in admin mode only!');
}

require_once $config->finalizePage;
