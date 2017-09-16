<?php
/**
 * 
 * $Id$
 * 
 */

require_once $config->classPath . '/modules/common.php';
require_once 'vp/Validation/Validation.php';

/**
 * 
 * 
 *   @package    modules
 *   @version    2002/07/19
 *   @access     public
 *   @author     Wolfram Kriesing <wolfram@kriesing.de>
 */
class modules_user extends modules_common
{

    var $table = TABLE_USER;

    function modules_user()
    {
        parent::modules_common();
    }

    function preset()
    {
        $this->reset();
        $this->setOrder('surname');
    }

    /**
     *   overwrite the remove method to check permissions, etc.
     * 
     */
    function remove($id)
    {
        global $config, $applError, $time;

        if (!$this->isAdmin()) {
            $applError->set('You are not allowed to remove users!');
            return false;
        }

        $id = (int) $id;

        //FIXXME check for references in TABLE_PRICE, and all the other tables too
        // require this here, so we can include the class at the beginning of init.php
        // if we would include it on top it would require a lot of other classes which require
        // instances that are not prepared at that point .. kinda messy

        // we can not require the class time here because it requires many others, see comment above
        $time = new modules_common(TABLE_TIME);
        $time->setWhere('user_id=' . $id);
        //print_r($time);
        if ($time->getCount()) {
            // AK, system worx : retrieve all logged times for that user and delete them !
            $result = $time->GetAll();
            //print_r($result); echo '<br>';
            foreach ($result as $data) {
                //echo "delete " . print_r($data, true) . '<br>';
                $time->remove($data);
            }
            unset($time);
            // read again number of users
            $time = new modules_common(TABLE_TIME);
            $time->setWhere('user_id=' . $id);
            if ($time->getCount()) {
                // something has gone wrong ...
                $applError->setOnce('You can not remove this user, because there are still times logged for him/her!');
                return false;
            }
        }

        // AK, system worx : retrieve all projects this user is assigned to and delete the assignement
        // do that only if we aren't this user and he isn't root : TODO
        $team = new modules_common(TABLE_PROJECTTREE2USER);
        $team->setWhere('user_id=' . $id);
        $result = $team->GetAll();
        //print_r($result); echo "<br>";
        $p = array();
        foreach($result as $data) {
                //echo "delete " . print_r($data, true) . '<br>';
                $team->remove($data);
                $p[] = $data['projectTree_id'];
        }

        // ToDo : I need to find a way to make the cache
        // invalid for forced rebuilding AKK, system worx

        //require_once $config->classPath . '/modules/project/cache.php';
        //modules_project_cache::setModifiedByProject($p);

        return parent::remove($id);
    }

    /**
     * 
     * 
     */
    function save($data)
    {
        global $applError, $applMessage, $config, $userAuth, $util;

        // AK : some issets added

        $data['login'] = trim($data['login']);
        $data['email'] = trim($data['email']);

        // AK : let the user change its password : see password.tpl ...
        if (!$this->isAdmin() && !(isset($data['newpwd']) && $data['newpwd'])) {
            $applError->set('You are not allowed to edit users!');
            return false;
        }

        if (isset($data['newpwd']) && $data['newpwd']) {
            // prevent id from form spoofing
            $data['id'] = $userAuth->getData('id');
            unset($data['newpwd']);
        }

        $ret = true;

        if (isset($data['email']) && !vp_Validation::isEmail($data['email'])) {
            $applError->set('Please enter a valid email-address!');
            $ret = false;
        }

        if (isset($data['hoursPerDay']) && !is_numeric($data['hoursPerDay'])) {
            $applError->set('The hours per day has to be a number!');
            $ret = false;
        }
        // if no hours are set store null, so no 0 appears
        if (!isset($data['hoursPerDay'])) {
            $data['hoursPerDay'] = null;
        }

        // if no password is given, remove the password from the $data,
        // so the old password wont be overwritten!
        if (empty($data['password']) || !isset($data['password'])) {
            unset($data['password']);
        }

        /**
         * Reset password stuff
         */
        if (isset($data['ResetPassword']) && $data['login'] != 'root') {
            $data['password'] = $this->randompass();
            $data['password1'] = $data['password'];
        }
        // EOF Reset Password

        // SX : We must not reset the password any longer when in LDAP mode
        // Now we CAN add non LDAP-users 
        if ((isset($data['password']) && isset($data['password1']))) {
            if ($data['password'] != $data['password1']) {
                $applError->set('The passwords don\'t match!');
                $ret = false;
            }
        } else {
            unset($data['password']);
        }

        if ((!empty($data['password']) && !empty($data['password1']))) {
            if ($data['password'] != $data['password1']) {
                $applError->set('The passwords don\'t match!');
                $ret = false;
            }
        } else {
            unset($data['password']);
        }

        if (!isset($data['password'])) {
            $mpwd = '';
        } else {
            $mpwd = $data['password'];
        }

        // we always need a login to be given!!!
        if (!isset($data['login']) || empty($data['login'])) {
            $applError->set('Please enter a valid login!');
            $ret = false;
        }

        // debug:
        //$applError->set(print_r($data, true));

        $canmail = true;

        if ($ret) {
            if (isset($data['sendInfoMail']) && !isset($data['ResetPassword'])) {
                if (!isset($data['email']) || empty($data['email'])) {
                    $applError->set('You have not given an e-mail to send the info mail to!');
                } else {
                    if ($canmail) {
                        $adminName = $userAuth->getData('name') . ' '
                                   . $userAuth->getData('surname');
                        $message =
                            "Hello {$data['name']} {$data['surname']},

                            $adminName has registered you for the opentimetool.
                            Your access data are:

                            username:   {$data['login']}
                            password:   {$mpwd}
                            (Please change your password right away!)

                            You can login here
                            {$config->vServerRoot}{$config->home}

                            best regards

                            $adminName
                        ";
                        // FIXXXME translate properly!!!
                        //$message = $util->translate($message);
                        $subject = 'Your timetool registration';
                        //$subject = $util->translate('Your timetool registration');
                        $mailAdditionalHeaders = (!empty($config->mailAdditionalHeaders))
                                               ? $config->mailAdditionalHeaders : NULL;
                        $mailAdditionalParameters = (!empty($config->mailAdditionalParameters))
                                                  ? $config->mailAdditionalParameters : NULL;
                        if (!mail($data['email'], $subject, $message, $mailAdditionalHeaders, $mailAdditionalParameters)) {
                            $applError->set("Error sending the mail to '{$data['email']}'!");
                        } else {
                            $applMessage->set("Info e-mail sent to '{$data['email']}'.");
                        }
                    }
                }
                if ($config->auth->savePwd &&
                        (isset($data['password']) && isset($data['password1']))) {
                    if ($data['password'] != $data['password1']) {
                        $applError->set('The passwords don\'t match!');
                        $ret = false;
                    }
                } else {
                    unset($data['password']);
                }
            }

            /**
             * Send Mail about password reset anyway
             */
            if (isset($data['ResetPassword'])) {
                if (!isset($data['email']) || empty($data['email'])) {
                    $applError->set('You have not given an e-mail to send the info mail to!');
                } else {
                    if ($canmail) {
                        $adminName = $userAuth->getData('name') . ' '
                                   . $userAuth->getData('surname');
                        $message =
                            "Hello {$data['name']} {$data['surname']},

                            $adminName has reset your password for the opentimetool.

                            Please login immediately and set a new one. 

                            Your username is:
                            username:              {$data['login']}
                            new random password:   {$mpwd}


                            (Please set your password right away!)

                            You can login here
                            {$config->vServerRoot}{$config->home}

                            best regards

                            $adminName
                        ";
                        // FIXXXME translate properly!!!
                        //$message = $util->translate($message);
                        $subject = 'Password reset in timetool by admin';
                        //$subject = $util->translate('Your timetool registration');
                        $mailAdditionalHeaders = (!empty($config->mailAdditionalHeaders))
                                               ? $config->mailAdditionalHeaders : NULL;
                        $mailAdditionalParameters = (!empty($config->mailAdditionalParameters))
                                                  ? $config->mailAdditionalParameters : NULL;
                        if (!mail($data['email'], $subject, $message, $mailAdditionalHeaders, $mailAdditionalParameters)) {
                            $applError->set("Error sending the mail to '{$data['email']}'!");
                        } else {
                            $applMessage->set("Info e-mail sent to '{$data['email']}'.");
                        }
                    }
                }
            }

            // AK: get rid of them; gives db errors as not in table
            unset($data['password1']);
            unset($data['ResetPassword']);

            return parent::save($data);
        }
        return $ret;
    }

    /**
     *   the checks that need to be done when updating the user data
     *
     *
     */
    function update($data)
    {
        global $config, $userAuth, $applError, $db;

        $ret = true;

        if ($curUser = $this->get($data['id'])) {
            // does the user want to change his username?
            if ($data['login'] != $curUser['login']) {
                // check if the login is still available, but dont check if it is the user's own :-)
                $this->reset();
                $this->setWhere('login=' . $db->quote($data['login']) . ' AND id <> ' . $data['id']);
                if ($this->getCount()) {
                    $applError->set('This login is not available anymore!');
                    $ret = false;
                }
                // we need the password to digest the password properly
                if (!$data['password']) {
                    $applError->set('Please enter a password in order to change the username!');
                    $ret = false;
                }
            }
        }

        // all the checks if the password is given twice and correct, etc. are done in the 'save' method
        // so we only need to digest it here
        // SX: well we digest now anyway even with ldap
        if (!empty($data['password'])) {
            $data['password'] = $userAuth->digest($data['login'], $data['password']);
        }

        if ($ret) {
            return parent::update($data);
        }
        return $ret;
    }

    /**
     *   check if enough user-licenses exist
     *
     */
    function add($data)
    {
        global $session, $applError, $config, $db, $userAuth;

        $ret = true;
        //FIXXXME get the data via xml-rpc to have the newset and
        // to be sure the session data are not used here!
        // since they can be manipulated
        $maxNumUsers = $session->account->numUsers;
        $this->reset();
        if ($maxNumUsers <= $this->getCount()) {
            $applError->set("Your license only allows $maxNumUsers users!");
            $ret = false;
        }

        if (!trim(@$data['surname']) || !trim(@$data['name'])) {
            $applError->set('Please enter the complete name!');
            $ret = false;
        }

        if (!isset($data['email'])) {
            $applError->set('Please enter a valid email-address!');
            $ret = false;
        }

        $this->reset();
        $this->setWhere('login=' . $db->quote($data['login']));
        if ($this->getCount()) {
            $applError->set('This login is not available anymore!');
            $ret = false;
        }

        if (!isset($data['password'])) {
            $applError->set('Please enter a password!');
            $ret = false;
        } else {
            $data['password'] = $userAuth->digest($data['login'], $data['password']);
        }

        if ($ret) {
            return parent::add($data);
        }
        return $ret;
    }

    /**
     *   add ldap authenticated user on the fly (SX)
     *   Password is generated and random
     *   This way no harm is done if authentication is changed to db afterwards ...
     */
    function add_ldap_user_passthrough($givenname, $surname, $userid, $mail)
    {
        global $session, $applError, $config, $db;

        $ret = true;

        $maxNumUsers = $session->account->numUsers;
        $this->reset();
        if ($maxNumUsers <= $this->getCount()) {
            $applError->set("Your license only allows $maxNumUsers users!");
            $ret = false;
        }

        if (!trim(@$surname) || !trim(@$givenname)) {
            $applError->set('Please enter the complete name!');
            $ret = false;
        }

        if (!isset($mail)) {
            $applError->set('Please enter a valid email-address!');
            $ret = false;
        }

        $this->reset();
        $this->setWhere('login=' . $db->quote($userid));
        if ($this->getCount()) {
            // should never happen as we checked that before in ldap auth
            $applError->set('This login is not available anymore!');
            $ret = false;
        }

        $password = $this->randompass();
        $data['password'] = $password;
        $data['surname']  = $surname;
        $data['name']     = $givenname;
        $data['email']    = $mail;
        $data['login']    = $userid;

        if ($ret) {
            return parent::add($data);
        }
        return $ret;
    }

    /**
     *   is the current user an admin
     * 
     *   @version    13/11/2002
     *   @author     Wolfram Kriesing <wk@visionp.de>
     *   @return boolean if the current user is an admin returns true
     */
    function isAdmin()
    {
        global $session;

        // AK : notice muss weg ;-)
        if (empty($session->temp)) {
            return false;
        }

        return isset($session->temp->user->isAdmin) && $session->temp->user->isAdmin;
    }

    /**
     *   switch to admin mode, this can only be done for the currently logged in user!!!
     * 
     */
    function adminModeOn()
    {
        global $session, $applError;

        if (!isset($session->temp)) {
            $session->temp = new stdClass();
        }
        if (!isset($session->temp->user)) {
            $session->temp->user = new stdClass();
        }
        $session->temp->user->isAdmin = $this->canBeAdmin() ? true : false;

        if (!$session->temp->user->isAdmin) {
            $applError->set('You can not switch to admin-mode!');
        }
        return $session->temp->user->isAdmin;
    }

    /**
     *   switch admin mode OFF, this is only be done for the currently logged in user!!!
     * 
     */
    function adminModeOff()
    {
        global $session;

        $session->temp->user->isAdmin = false;
    }

    function canBeAdmin($userId = null)
    {
        global $userAuth;

        if ($userId == null) {
            $canBeAdmin = $userAuth->getData('isAdmin');
        } else {
            $canBeAdmin = $this->get($userId, 'isAdmin') ? true : false;
        }

        return $canBeAdmin;
    }

    /**
     *   this gets all available users, the current person can see
     *   this is actually needed for the overview-filter
     *
     *   @return array   just like getAll, only filtered data
     */
    function getAllAvail()
    {
        global $userAuth;

        $this->preset();
        // the admin can see all the users
        if (!$this->isAdmin()) {
            $myUid = $userAuth->getData('id');

            // get all the projectTree_id's of the projects where i am
            // manager of
            $tree2user = new modules_common(TABLE_PROJECTTREE2USER);
            $tree2user->setWhere("user_id=$myUid AND isManager=1");
            $tree2user->setSelect('projectTree_id');
            $tree2user->setGroup('projectTree_id');
            $projectIds = array();
            if (sizeof($myProjects = $tree2user->getAll())) {
                foreach ($myProjects as $aProject) {
                    $projectIds[] = $aProject['projectTree_id'];
                }
            }

            // get the user-data, for those users that are member in any of my projects
            // if i am not a manager in any project, then get only my user data
            $this->autoJoin(TABLE_PROJECTTREE2USER);
            if (sizeof($projectIds)) {
                $this->addWhere('projectTree_id IN (' . implode(',', $projectIds) . ')');
            } else {
                $this->addWhere("id=$myUid");
            }
            $this->setSelect(TABLE_USER . '.*');
            $this->setGroup('id');
        }
        return $this->getAll();
    }

    /**
     * SX : simple (!) function to create a pseudo-random password
     */
    function randompass()
    {
        mt_srand((double) microtime() * 1000000);

        $new = '';
        $len = 12;
        $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $max = strlen($str) - 1;
        for ($i=0; $i < $len; $i++) {
            $new .= $str[mt_rand(0, $max)];
        }

        return $new;
    }

} // end of class

$user = new modules_user;
