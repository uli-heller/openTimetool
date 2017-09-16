<?php
/**
 * 
 * $Id$
 * 
 */

/**
 *   this class shall only to be used as static
 *   since it caches data internally we only need on instance throughout the entire application
 *   it is simply more efficient
 * 
 *   @static
 *   @package    modules
 *   @version    2002/10/25
 *   @access     public
 *   @author     Wolfram Kriesing <wolfram@kriesing.de>
 */
class modules_project_member extends modules_common
{

    var $table = TABLE_PROJECTTREE2USER;

    /**
     *   @var    array   this is an array where the user-id is the index
     *                   and inside are the data for the projects this user is a manager for
     */
    var $_cachedManagers = array();

    /**
     *   @var    array   this is an array where the user-id is the index
     *                   and inside are the data for the projects this user is a member of
     */
    var $_cachedMembers = array();

    /**
     *
     *   @access     public
     *   @version    2002/10/25
     *   @author     Wolfram Kriesing <wk@visionp.de>
     */
    function modules_project_member()
    {
        parent::modules_common();
        $this->preset();
    }

    function &getInstance()
    {
        global $projectMember;

        // AK : very strange statement ...
        //if (!strtolower(get_class($projectMember)) == 'modules_project_member') {
        //    $projectMember =& new modules_project_member();
        //}
        // do it now like this :	
        if (isset($projectMember)) {
            if (strtolower(get_class($projectMember)) != 'modules_project_member') {
            	$projectMember = new modules_project_member();
            }
        } else {
            $projectMember = new modules_project_member();
        }

        return $projectMember;
    }

    /**
     *   this does a reset and sets the initial state as we think we mostly need it :-)
     * 
     *   @access     public
     *   @version    2002/10/25
     *   @author     Wolfram Kriesing <wk@visionp.de>
     */
    function preset()
    {
        $this->reset();
        $this->autoJoin(TABLE_USER);
        $this->setOrder(TABLE_USER . '.surname,' . TABLE_USER . '.name');
    }

    /**
     *   adds members to the given project
     * 
     *   @access     public
     *   @version    2002/10/25
     *   @author     Wolfram Kriesing <wk@visionp.de>
     *   @param      array   an array of user ids
     *   @param      int     the project-id (projectTree_id)
     *   @param      boolean if true the member is added as a manager,
     *                       otherwise as a normal team member
     *   @return     boolean true if successful
     */
    function addMember($userIds, $projectId, $isManager = 0)
    {
        $data = array();
        foreach ($userIds as $aUserId) {
            $data[] = array(
                'user_id'        => $aUserId,
                'projectTree_id' => $projectId,
                'isManager'      => $isManager,
            );
        }

        return $this->addMultiple($data);
    }

    /**
     *   adds a manager to a project
     * 
     *   @access     public
     *   @version    2002/10/25
     *   @author     Wolfram Kriesing <wk@visionp.de>
     *   @param      array   an array of user ids
     *   @param      int     the project-id (projectTree_id)
     *   @return     boolean true if successful
     */
    function addManager($userIds, $projectId)
    {
        return $this->addMember($userIds, $projectId, 1);
    }

    /**
     *   this is really a special update
     *   it takes the data and updates the managers and members
     *   accordingly. First it updates their state either to manager or to member
     *   than it adds the missing entries in the DB
     *   and uses removeMember to remove members which are not given anymore.
     *   Using removeMember is necessary, since that method also checks 
     *   if the person can be removed from the project, this is not possible
     *   if this member has already booked times on the current project.
     *
     *   @param  array   the user_id's of the members
     *   @param  array   the user_id's of the managers
     */
    function updateSpecial($members, $managers, $projectId)
    {
        global $config;

        settype($members, 'array');
        settype($managers, 'array');
        //
        //  1.  Update members that are already in the DB, set their state according to $data
        //
        require_once $config->classPath . '/modules/project/cache.php';
        modules_project_cache::setModified($members);
        modules_project_cache::setModified($managers);
/*
print "<br>members=<br>";
print_r($members);
print "<br>managers=<br>";
print_r($managers);
*/
        $this->reset();
        if (sizeof($members)) {
            // update all the given users and set them to be a member
            $this->setWhere("projectTree_id=$projectId AND user_id IN (" . implode(',', $members) . ")");
            $this->update(array('isManager' => 0));
        }
        if (sizeof($managers)) {
            // update all the given users and set them to be a manager
            $this->setWhere("projectTree_id=$projectId AND user_id IN (" . implode(',', $managers) . ")");
            $this->update(array('isManager' => 1));
        }

        //
        //  2.  Add members, that are not in the DB yet!
        //
        $allUserIdsInDb = array();
        $userIds = array();
        $userIds = array_merge($members, $managers);
        $removeIds = array();
        $removeUserIds = array();
        $this->setSelect('id,user_id');
        $this->setWhere("projectTree_id=$projectId");
        if ($_members = $this->getAll()) {
            foreach ($_members as $aMember) {
                // if the user_id is already in the DB and in one of those that we had updated
                // then we simply ignore it, since it already has the right value
                // if not, then we have to remove it
                if (!in_array($aMember['user_id'], $userIds)) {
                    $removeIds[] = $aMember['id'];
                    $removeUserIds[] = $aMember['user_id'];
                }
                $allUserIdsInDb[] = $aMember['user_id'];
            }
        }
        if (sizeof($missingManagers = array_diff($managers, $allUserIdsInDb))) {
            $newData = array();
            foreach ($missingManagers as $aAdd) {
                $newData[] = array(
                    'user_id'        => $aAdd,
                    'isManager'      => 1,
                    'projectTree_id' => $projectId,
                );
            }
            $this->addMultiple($newData);
        }
        if (sizeof($missingMembers = array_diff($members, $allUserIdsInDb))) {
            $newData = array();
            foreach ($missingMembers as $aAdd) {
                $newData[] = array(
                    'user_id'        => $aAdd,
                    'isManager'      => 0,
                    'projectTree_id' => $projectId,
                );
            }
            $this->addMultiple($newData);
        }

        //
        //  3.  remove all the members that are not in the actual $data
        //      (until now we did only update and add!!! so we need to remove now)
        //
        $this->removeMember($removeIds);
        modules_project_cache::setModified($removeUserIds);

// FIXXXME return true if update succeeded
        return true;
    }

    /**
     *   removes any kind of team member
     *   it checks before if this has booked any times on this project before,
     *   if he had done so the user can't be removed
     * 
     *   @access     public
     *   @version    2002/10/25
     *   @author     Wolfram Kriesing <wk@visionp.de>
     *   @param      int     the id of the entry in the table projectTree2User
     *   @return     boolean true if successful
     */
    function removeMember($ids)
    {
        global $applError;

        settype($ids,'array');
        if (!sizeof($ids)) {
            return true;
        }

        $this->preset();
        $this->addJoin(TABLE_TIME, 'projectTree_id=' . TABLE_TIME . '.projectTree_id ' .
                                   'AND ' . TABLE_USER . '.id=' . TABLE_TIME . '.user_id');
        $this->addWhere('id IN (' . implode(',', $ids) . ')');
        $this->setGroup(TABLE_USER . '.id');
        if ($users = $this->getAll()) {
            $removeIds = array();
            foreach ($users as $aUser) {
                $applError->setOnce("{$aUser['_user_name']} {$aUser['_user_surname']} " .
                    "has already booked times on this project, user can't be removed!");
                $removeIds[] = $aUser['id'];
            }
        }

        // AK : isset added to avoid php notices
        if ((isset($removeIds)) && sizeof($removeIds)) {
            foreach ($ids as $key => $aId) {
                if (in_array($aId, $removeIds)) {
                    unset($ids[$key]);
                }
            }
        }

        if (sizeof($ids)) {
            return $this->removeMultiple($ids);
        }
        return false;
    }

    /**
     *   check if the user is a project member
     *   if no params are given the user needs to be a member for at least one project
     *   for this function to return true
     * 
     *   @access     public
     *   @version    2002/10/25
     *   @author     Wolfram Kriesing <wk@visionp.de>
     *   @param      int     the id of the entry in the table projectTree2User
     *   @return     boolean true if successful
     */
    function isMember($projectId = null, $userId = null)
    {
        global $userAuth;

        if ($userId == null) {
            if ($userAuth->isLoggedIn()) {
                $userId = $userAuth->getData('id');
            } else {
                return false;
            }
        }

        $isMemberIn = &$this->_cachedMembers[$userId];
        // any data cached?
        if (!$isMemberIn) {
            $this->preset();
            $this->setWhere('user_id=' . $userId);
            $this->setIndex('projectTree_id');
            $isMemberIn = $this->getAll();
        }

        if ($projectId == null && sizeof($isMemberIn)) {
            return true;
        }
        if ($projectId && @$isMemberIn[$projectId]) {
            return true;
        }

        return false;
    }

    /**
     *   check if the user is a project manager
     *   if no params are given the user needs to be a manager for at least one project
     *   for this function to return true
     * 
     *   @access     public
     *   @version    2002/10/25
     *   @author     Wolfram Kriesing <wk@visionp.de>
     *   @param      int     the id of the entry in the table projectTree2User
     *   @return     boolean true if successful
     */
    function isManager($projectId = null, $userId = null)
    {
        global $userAuth;

        //echo 'classes member ','ismanager:this->_cachedmanagers : '.print_r($this->_cachedManagers,true)."<br>";
        //echo 'classes member ','ismanager:this->_cachedmembers : '.print_r($this->_cachedMembers,true)."<p>";

        $this->isMember(null, $userId);

        if ($userId == null) {
            if ($userAuth->isLoggedIn()) {
                $userId = $userAuth->getData('id');
            } else {
                return false;
            }
        }

        // AK : check if userID in cachedManagers -> I hate PHP notices !
        // original code :
        // $manages = $this->_cachedManagers[$userId];
        //foreach( $this->_cachedMembers[$userId] as $aMember ) {
        //    if ($aMember['isManager'])
        //        $manages[$aMember['projectTree_id']] = $aMember;
        //}
        // After quite a while of debugging I found that $this->cachedManagers is ALWAYS empty ...
        // that should be fixed one day to have a cache here as well or delete the whole
        // chachedManagers-stuff completely ...
        //AK
        if (isset($this->_cachedManagers[$userId])) {
            $manages = $this->_cachedManagers[$userId];
        }

    	foreach ($this->_cachedMembers[$userId] as $aMember) {
            if ($aMember['isManager']) {
                $manages[$aMember['projectTree_id']] = $aMember;
            }
        }

        // AK : isset due to notice
        if ($projectId == null && isset($manages)) {
            return true;
        }
        // AK : isset due to notice
        if ($projectId && isset($manages[$projectId])) {
            return true;
        }

        return false;
    }

    /**
     * get the projects where the user with the given user id is member in
     */
    function getMemberProjects($userId = null)
    {
        global $userAuth;

        $this->isMember(null, $userId);

        if ($userId == null) {
            if ($userAuth->isLoggedIn()) {
                $userId = $userAuth->getData('id');
            } else {
                return false;
            }
        }

        return $this->_cachedMembers[$userId];
    }

    /**
     * get the projects where the user with the given user id is member in
     */
    function getManagerProjects($userId = null)
    {
        $ret = array();
        $memberProjects = $this->getMemberProjects($userId);
        if (sizeof($memberProjects)) {
            foreach ($memberProjects as $aProject) {
                if ($this->isManager($aProject['projectTree_id'], $userId)) {
                    $ret[] = $aProject;
                }
            }
        }
        return $ret;
    }

} // end of class

$projectMember = modules_project_member::getInstance();

if (!$config->isLiveMode()) {
//    include_once $config->applRoot . '/logging.php';
//    $logging->_logme('classes member', 'projectMember : ' . print_r($projectMember, true));
}
