<?php
/**
 * Created on Aug 28, 2006
 * 
 * $Id$
 * 
 * It is so hard to debug that stuff, that we create a logging utility
 * which can be included in any script
 * 
 * A call of the function with
 *             $this->_log('call autoAuth','setup',__LINE__);
 *  (when paramlist is like this  _log( $msg,$method=0,$line=0))
 * 
 * leads to an entry like this
 * 	Aug 28 15:16:27  [info] [Auth::setup] call autoAuth (375)
 * 
 * I have stolen that piece of code from classes/common.oho
 * and modified it a bit to be more flexible useable
 * 
 * $section would be something like the 'Auth' from above example
 * 
 * options['logFile'] is set in init.php ! Look there
 * 
 */

/**
 * log various messages in a file named 'develop.log' in the tmpdir of openTimetool
 * 
 */
class logging
{

    /**
     *   the instance of the Log-class
     */
    var $_log = null;

    function _logme($section, $msg, $method = 0, $line = 0)
    {
        global $options, $config;

        $logfile = $config->tmpDir . '/develop.log';

        if (!isset($this->_log)) {
            require_once 'Log.php'; // PEAR module !
            $this->_log = &Log::factory('file', $logfile);
            $this->_log->log('-----START----');
            $this->_log->log($_SERVER['PHP_SELF']);
        }

        $this->_log->log('[' . $section . ($method?"::$method":'') .
                "] $msg" . ($line?" ($line)":''));
    }

}

if (!isset($logging)) {
    $logging = new logging;
}
