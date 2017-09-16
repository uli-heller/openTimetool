<?php
/**
 * 
 * $Id$
 * 
 */

/**
 *
 *
 *   @package    modules
 *   @version    2002/11/26
 *   @access     public
 *   @author     Wolfram Kriesing <wolfram@kriesing.de>
 */
class modules_account // extends modules_common
{

    /**
     *   @var    int     the number of users allowed to use this application
     *                   does only have an effect on the NET-version
     */
    var $numUsers = 10; // the default (AK)

    var $features = array(
        'translate',
        //'price',
    );

    /**
     *   remember if the account has changed, so we can check it
     *   to logout the user!
     */
    var $accountChanged = false;

    /**
     *   if the name has changed we need to get the new data for this account
     *   since we dont have a subdomain and a cookie for each account seperatly
     */
    function modules_account()
    {
        global $session, $config;

        // AK : retrieve number of numUsers from config.php now
        if (isset($config->numUsers)) {
            $this->numUsers = $config->numUsers;
        }

        // AK : eliminate php notice on first start
        if (isset($session->accountName)) {
            $oldAccountName = $session->accountName;
        } else {
            @$session->accountName = '';
        }

        // AK : eliminate php notice on first start
        if (!isset($oldAccountName) || $oldAccountName != $this->getAccountName()) {
            $this->accountChanged = true;
        }
        // do also honor not asp versions, since they also need account data :-)
        // AK : previously $this->isAspVersion ?!?!? -> added brackets as it is a method !
        if (!$this->isAspVersion() || $this->accountChanged) {
            $this->prepare(true);
        }
    }

    /**
     * 
     */
    function prepare($force = false)
    {
        global $session, $applError;

        if ($this->isAspVersion()) {
            if ($this->getAccountName()) {
                $this->_processXMLRPC($this->getAccountName(), $force);
            }
        } else {
            $this->_processDefault();
        }

        $this->setFeatures();
    }

    /**
     * 
     */
    function setFeatures()
    {
        global $config, $session;

        if ($session->account->features) {
            foreach ($session->account->features as $aFeature) {
                $config->setFeature($aFeature, true);
            }
        }
    }

    function isAspVersion()
    {
        global $config;

        // do it more propely, A:K, system worx
        if (isset($config->backOffice->host) && isset($config->backOffice->path)) {
            return true;
        }
        //return @$config->backOffice->host && @$config->backOffice->path;
        return false;
    }

    /**
     * this processes the data in the default mode, which means that this is not an ASP version
     */
    function _processDefault()
    {
        global $session;

        //$session->account->data = $data;
        unset($session->account);
        $session->account = new stdClass();
        $session->account->numUsers = $this->numUsers;
        $session->account->features = $this->features;
        $session->account->isActive = true;
    }

    function getAccountName()
    {
        global $session, $config;
/*
        // in a live version get the account name by the subdomain!
        if (!$session->accountName && $config->isLiveMode()) {
            $hostname = explode('.', $_SERVER['HTTP_HOST']);
            $session->accountName = str_replace('timetool-', '', $hostname[0]);
        }
*/
        if ($config->isLiveMode()) {
            // AK strange stuff ! Don't know any VPCUST index ...
            // only line $session->accountName = $_SERVER['VPCUST']; embedded in if
            if (isset($_SERVER['VPCUST'])) {
                $session->accountName = $_SERVER['VPCUST'];
            } else {
                $session->accountName = '';
            }
        }

        return $session->accountName;
    }

    function setAccountName($name = null)
    {
        global $session;

        if (!$name) {
            unset($session->accountName);
        } else {
            $session->accountName = $name;
        }
    }

    /**
     * this method gets the data via XMLRPC and writes them in the session
     */
    function _processXMLRPC($accountName, $force = false)
    {
        global $config, $session, $applError, $applMessage;

        // do we already have the account data?
        if ($force || !$session->account->data) {
            include_once $config->classPath . '/modules/remote/remote.php';
            $remote = new modules_remote();

            unset($session->account);
            $session->account->data      = $remote->execute('account.get', $accountName);
            $session->account->numUsers  = $session->account->data['numUsers'];
            $session->account->expires   = $session->account->data['endTime'];
            $session->account->ttVersion = $session->account->data['_version_name'];

            if (!$config->isLiveMode()) {
                $applError->log('NO-ERROR: getting data via XML-RPC, dbName=' .
                    $session->account->data['dbName'] . ', tableName=' .
                    $session->account->data['tablePrefix']
                );
            }

            $session->account->features = $remote->execute(
                'account.getFeatures', $accountName
            );
            $session->account->isActive = modules_remote::execute(
                'account.isActive', $this->getAccountName()
            );
        }
    }

    function isActive()
    {
        global $session;

        return $session->account->isActive;
    }

}

$account = new modules_account();
