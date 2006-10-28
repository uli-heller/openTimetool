<?php
/**
 * 
* $Id
*
* 
* ########### switch to subversion ##########
*  $Log: user.php,v $
*  Revision 1.14.2.1  2003/03/11 16:05:08  wk
*  - dont translate mail yet
*
*  Revision 1.14  2003/02/13 16:13:11  wk
*  - send info mail
*
*  Revision 1.13  2003/02/10 15:59:59  wk
*  - added getAllAvail
*  - forgot reset in preset
*  - CS
*
*  Revision 1.12  2002/12/09 16:43:46  wk
*  - bugfix when changing username and password
*
*  Revision 1.11  2002/12/05 14:18:32  wk
*  - check input field values, and digest password if needed
*
*  Revision 1.10  2002/12/04 15:49:55  wk
*  - allow only admins to access and modify user data
*
*  Revision 1.9  2002/11/30 13:02:43  wk
*  - check password if entered
*
*  Revision 1.8  2002/11/29 16:54:00  wk
*  - added preset method
*  - fix the number of users check
*
*  Revision 1.7  2002/11/27 08:03:31  wk
*  - moved account stuff into account class
*
*  Revision 1.6  2002/11/26 15:59:12  wk
*  - added getAccountData
*  - check numUsers when adding users
*
*  Revision 1.5  2002/11/13 18:59:31  wk
*  - added all the admin methods
*
*  Revision 1.4  2002/10/28 11:18:09  wk
*  - require class inside method, so we can include this file in the init.php on top
*
*  Revision 1.3  2002/10/24 14:09:09  wk
*  - check the email when saving the data
*
*  Revision 1.2  2002/08/29 16:45:04  wk
*  - allow removing user only if there are no times for this user
*
*  Revision 1.1.1.1  2002/07/22 09:37:37  wk
*
*
*/

require_once($config->classPath.'/modules/common.php');
require_once('vp/Validation/Validation.php');

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
    function remove( $id )
    {
        global $config,$applError,$time;

        if (!$this->isAdmin()) {
            $applError->set('You are not allowed to remove users!');
            return false;
        }

//FIXXME check for references in TABLE_PRICE, and all the other tables too
        // require this here, so we can include the class at the beginning of init.php
        // if we would include it on top it would require a lot of other classes which require
        // instances that are not prepared at that point .. kinda messy
        $time = new modules_common(TABLE_TIME); // we can not require the class time here because it requires many others, see comment above
        $time->setWhere('user_id='.$id);
        if ($time->getCount()) {
            $applError->setOnce('You can not remove this user, because there are already times logged for him/her!');
            return false;
        }
        return parent::remove($id);
    }

    /**
    *
    *
    */
    function save( $data )
    {
        global $applError,$applMessage,$config,$userAuth,$util;
            
        // AK : some issets added 
             
        $data['login'] = trim($data['login']);
        $data['email'] = trim($data['email']);

        if (!$this->isAdmin()) {
            $applError->set('You are not allowed to edit users!');
            return false;
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
        if (!isset($data['hoursPerDay'])) { // if no hours are set store null, so no 0 appears
            $data['hoursPerDay'] = null;
        }

        // if no password is given, remove the password from the $data, so the old password wont be overwritten!
        if(!isset($data['password'])) {
            unset( $data['password'] );
        }

        if ($config->auth->savePwd && ($data['password'] || $data['password1'])) {
            if ($data['password'] != $data['password1']) {
                $applError->set('The passwords don\'t match!');
                $ret = false;
            }
        } else {
            unset( $data['password'] );
        }

        if (!isset($data['login']) ) {  // we always need a login to be given!!!
            $applError->set('Please enter a valid login!');
            $ret = false;
        }

        if ($ret) {
            if (isset($data['sendInfoMail'])) {
                if (!isset($data['email'])) {
                    $applError->set('You have not given an e-mail to send the info mail to!');
                } else {
                    $adminName = $userAuth->getData('name').' '.$userAuth->getData('surname');
                    $message =
"Hello {$data['name']} {$data['surname']},

$adminName has registered you for the v:p timetool.
Your access data are:

username:   {$data['login']}
password:   {$data['password']}
(Please change your password right away!)

You can login here
    {$config->vApplRoot}{$config->home}

best regards

$adminName
";
// FIXXXME translate properly!!!
//                    $message = $util->translate($message);
                    $subject = 'Your timetool registration';
//                    $subject = $util->translate('Your timetool registration');
                    if (!@mail($data['email'],$subject,$message)) {
                        $applError->set("Error sending the mail to '{$data['email']}'!");
                    } else {
                        $applMessage->set("Info e-mail sent to '{$data['email']}'.");
                    }
                }
            }

            return parent::save($data);
        }
        return $ret;
    }

    /**
    *   the checks that need to be done when updating the user data
    *
    *
    */
    function update( $data )
    {
        global $config, $userAuth, $applError, $db;

        $ret = true;

        if( $curUser = $this->get( $data['id'] ) )
        {
            if( $data['login'] != $curUser['login'] )   // does the user want to change his username?
            {                  
                // check if the login is still available, but dont check if it is the user's own :-)
                $this->reset();
                $this->setWhere( 'login='.$db->quote($data['login']).' AND id<>'.$data['id'] );
                if( $this->getCount() )
                {
                    $applError->set('This login is not available anymore!');
                    $ret = false;
                }

                if( !$data['password'] )            // we need the password to digest the password properly
                {
                    $applError->set('Please enter a password in order to change the username!');
                    $ret = false;
                }
            }
        }

        // all the checks if the password is given twice and correct, etc. are done in the 'save' method
        // so we only need to digest it here
        if( $config->auth->savePwd && $data['password'] )
        {
            $data['password'] = $userAuth->digest( $data['login'] , $data['password'] );
        }

        if( $ret )
            return parent::update( $data );
        return $ret;
    }

    /**
    *   check if enough user-licenses exist
    *
    */
    function add( $data )
    {
        global $session, $applError, $config, $db, $userAuth;
                  
        $ret = true;
//FIXXXME get the data via xml-rpc to have the newset and to be sure the session data are not used here!
// since they can be manipulated
        $maxNumUsers = $session->account->numUsers;
        $this->reset();
        if( $maxNumUsers <= $this->getCount() )
        {
            $applError->set("Your license only allows $maxNumUsers users!");
            $ret = false;
        }

        if( !trim($data['surname']) || !trim($data['name']) )
        {
            $applError->set('Please enter the complete name!');
            $ret = false;
        }

        if( !$data['email'] )
        {
            $applError->set('Please enter a valid email-address!');
            $ret = false;
        }

        $this->reset();
        $this->setWhere( 'login='.$db->quote($data['login']) );
        if( $this->getCount() )
        {
            $applError->set('This login is not available anymore!');
            $ret = false;
        }
              
        if( !$data['password'] )
        {
            $applError->set('Please enter a password!');
            $ret = false;
        }    
        else
        {
            $data['password'] = $userAuth->digest( $data['login'] , $data['password'] );
        }

        if( $ret )
            return parent::add( $data );
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
        if(empty($session->temp)) return false;
        else
	        return isset($session->temp->user->isAdmin) && $session->temp->user->isAdmin;
    }
                          
    /**
    *   switch to admin mode, this can only be done for the currently logged in user!!!
    *
    */
    function adminModeOn()
    {
        global $session,$applError;

        $session->temp->user->isAdmin = $this->canBeAdmin() ? true : false;

        if( !$session->temp->user->isAdmin )
        {
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

    function canBeAdmin( $userId=null )
    {                         
        global $userAuth;

        if( $userId == null )
            $canBeAdmin = $userAuth->getData('isAdmin');
        else
            $canBeAdmin = $this->get($userId,'isAdmin') ? true : false;

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
            if (sizeof($myProjects = $tree2user->getAll()) ) {
                foreach ($myProjects as $aProject) {
                    $projectIds[] = $aProject['projectTree_id'];
                }
            }

            // get the user-data, for those users that are member in any of my projects
            // if i am not a manager in any project, then get only my user data
            $this->autoJoin(TABLE_PROJECTTREE2USER);
            if (sizeof($projectIds)) {
                $this->addWhere('projectTree_id IN ('.implode(',',$projectIds).')');
            } else {
                $this->addWhere("id=$myUid");
            }
            $this->setSelect(TABLE_USER.'.*');
            $this->setGroup('id');
        }
        return $this->getAll();
    }

}   // end of class

$user = new modules_user;

?>
