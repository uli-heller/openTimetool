<?php
//
//  $Log: tree.php,v $
//  Revision 1.16.2.1  2003/03/17 16:25:40  wk
//  - overwrite add() to update cache info
//
//  Revision 1.16  2003/03/06 11:07:06  wk
//  - use static tree-cache class
//
//  Revision 1.15  2003/03/04 19:08:53  wk
//  - added getInstance() and setup()
//  - added all the caching methods
//
//  Revision 1.14  2003/02/10 19:11:59  wk
//  - use updated Tree::getPathAsString method
//
//  Revision 1.13  2003/02/10 12:16:22  wk
//  - just some CS'ing
//
//  Revision 1.12  2002/11/13 18:58:25  wk
//  - do some extra update checking and admin checking
//
//  Revision 1.11  2002/10/28 19:06:18  wk
//  - show tree to member projects properly
//
//  Revision 1.10  2002/10/28 11:17:15  wk
//  - projects are only available when the user is a member in them
//
//  Revision 1.9  2002/10/22 18:19:23  wk
//  - added isClosed
//
//  Revision 1.8  2002/09/23 09:33:47  wk
//  - only replaced # by //
//
//  Revision 1.7  2002/08/26 09:07:41  wk
//  - removed old code
//  - bugfix in unfold
//  - add comments
//
//  Revision 1.6  2002/08/22 12:40:56  wk
//  - made unfolding work properly
//  - added unfoldAll
//
//  Revision 1.5  2002/08/20 16:21:48  wk
//  - added methods which are used for folding the project tree
//
//  Revision 1.4  2002/08/19 20:30:43  wk
//  - added *Available methods
//
//  Revision 1.3  2002/07/30 20:22:07  wk
//  - remove the 'Root' prefix
//
//  Revision 1.2  2002/07/24 17:05:48  wk
//  - get the path usually as a string with a - as the seperator
//
//  Revision 1.1  2002/07/23 13:47:02  wk
//  - initial commit
//
//

require_once 'Tree/Memory.php';
require_once $config->classPath.'/modules/project/member.php';
require_once $config->classPath.'/modules/project/cache.php';
require_once $config->classPath.'/modules/time/time.php';


/**
*   do only instanciate this class calling getInstance
*       $projectTree = modules_project_tree::getInstance(true);
*
*
*/
class modules_project_tree extends Tree_Memory
{

    /**
    *   Return an instance of projectTree
    *   
    *   @param  boolean     do setup the tree
    */
    function &getInstance()
    {
        global $projectTree,$config;
        // is projectTree an instance of this class?
        if (!strtolower(get_class($projectTree))=='modules_project_tree') {
            $projectTree =& new modules_project_tree(   'DBnested',$config->dbDSN,
                                                        array('table'=>TABLE_PROJECTTREE));
            $projectTree->setup();
        }
        return $projectTree;
    }

    /**
    *   this method makes reading the tree much more effective and less consuming
    *   it reads the tree only as it is needed for the current user
    *   this way not the entire tree is read every time as it used to be
    *   This is only possible with the updated Tree class which allows to 
    *   pass the data to setup()
    *
    *   @version    25/02/2003
    *   @author     Wolfram Kriesing <wk@visionp.de>
    *
    */
    function setup()
    {
        global $userAuth,$user;
        $userId = $userAuth->getData('id');  
        $orderBy = 'name';
        
        $data = null; // AK : we avoid notices in initial state like that
        
        // first get all 'left' and 'right' columns, so we can build the query
        // which gets all elements down the tree-path down to the root
        // because we need the entire path to build a proper tree :-) not only the 
        // nodes where the user has access!
        $tree = new modules_common(TABLE_PROJECTTREE);        
        if ($user->isAdmin()) {
            $tree->setLeftJoin(TABLE_PROJECTTREE2USER,TABLE_PROJECTTREE.'.id='.TABLE_PROJECTTREE2USER.'.projectTree_id AND '.TABLE_PROJECTTREE2USER.'.user_id='.$userId);
            $tree->setOrder($orderBy);
            $data = $tree->getAll();
        } else {
            $tree->reset();
            $tree->autoJoin(TABLE_PROJECTTREE2USER);
            $tree->setWhere(TABLE_PROJECTTREE2USER.'.user_id='.$userId);
            $tree->setSelect('id,l,r');
            if ($allNodes = $tree->getAll()) {
                // get all the tree-ids that we need to show the tree properly
                // note that not all those nodes are accessable for the user
                // but we need them to build aproper tree
                // we only get the ids here, since we have a 'group by' and if we would
                // get the data right here we would not be SQL92 compatible (only mysql-compatible)
                $tree->reset();
                $tree->autoJoin(TABLE_PROJECTTREE2USER);
                $tree->setWhere(TABLE_PROJECTTREE2USER.'.user_id='.$userId);
                foreach ($allNodes as $aNode) {
                    $where = "(l<={$aNode['l']} AND r>={$aNode['r']})";
                    $tree->addWhere($where,'OR');
                }
                $tree->setGroup(TABLE_PROJECTTREE.'.id');
                $treeIds = $tree->getCol('id');
                
                $tree->reset();
                $tree->setLeftJoin(TABLE_PROJECTTREE2USER,TABLE_PROJECTTREE.'.id='.TABLE_PROJECTTREE2USER.'.projectTree_id');
                $tree->setOrder($orderBy);
                $tree->setWhere(TABLE_PROJECTTREE.'.id IN ('.implode(',',$treeIds).')');                
                $data = $tree->getAll();
            }
        }
        //var_dump($data);echo "<p>";
        return parent::setup($data);
//$this->varDump();        
    }

    function getPathAsString( $id )
    {
        return parent::getPathAsString($id,' | ',1);
    }

    /**
    *   just a wrapper to be compatible to vp_DB_Common
    *
    */
    function &getAll()
    {
        return $this->getNode();
    }

    /**
    *   get only the projects which's startdate-enddate period
    *   is valid, all the active projects only
    *
    */
    function getAllAvailable()
    {
        $now = time();
        $tree = $this->getNode();
        if( sizeof($tree) )
        {
            //$removedNodes = array();
            foreach( $tree as $key=>$aNode )
            {
                $curId = $aNode['id'];
                if( //($removedNodes[$this->getParentId($curId)]) ||  // if the parent was already removed
                    !$this->isAvailable( $aNode , $now )
                  )
                {
                    //$removedNodes[$curId] = true;
                    unset($tree[$key]);
                }
            }
        }
        return $tree;
    }

    /**
    *   checks if the node passed to this method is an available project
    *   returns true if so          
    *   for an admin we ONLY need to check if the $timeToCompareTo is within
    *   the valid period of time, for which the project is valid, we dont need to check
    *   - if the project is closed for last month (the 'X days' thing)
    *   - if he is a team member
    *
    *   @param  mixed   (1) array - one row of projectTree
    *                   (2) int - the id of a row, needs to be retreived from the DB
    *   @param  int     if given this is the time used to compare the availability to
    *                   if not given the current time is used
    *   @return boolean true if project is still available, false otherwise
    */
    function isAvailable( $node , $timeToCompareTo )
    {              
        global $user;

        if( !is_array($node) )
        {
            $node = $this->getElement($node);
        }

        if( $timeToCompareTo==null )
            $timeToCompareTo = time();

        $nodes = $this->getParents( $node['id'] );  // returns the node including $node['id']
        $nodes = array_reverse($nodes);             // we foreach through it starting from the element itself, so we return hopefully as soon as possible
                               
        // check the valid-period of a project, only bookings within this time are allowed!!!
        foreach( $nodes as $aNode )
        {
            if(
                (
                    ($aNode['startDate']!=0 && $aNode['endDate']!=0) &&   // if the start and end date are given
                    !(  $aNode['startDate'] < $timeToCompareTo &&
                        ($aNode['endDate']+24*60*60) > $timeToCompareTo // and the time period is not valid now
                    )
                ) ||
                (   // if only a start date is given, dont check enddate
                    ( $aNode['startDate']!=0  && $aNode['endDate']==0) &&
                    $aNode['startDate'] > $timeToCompareTo
                ) ||
                (   // if only a end date is given, dont check start
                    ( $aNode['startDate']==0  && $aNode['endDate']!=0) &&
                    ($aNode['endDate']+24*60*60) < $timeToCompareTo
                )
            )
            {
                return false;
            }
        }
                        
        // !!!! the rest of the checks dont apply to an admin !!!
        if ($user->isAdmin()) {
            return true;
        }

        // can the current user book on the project, for the given $timeToCompareTo
        // this is the 'X days'-thing, after how many days in a new month a project is closed
        if ($this->isClosed( $node , $timeToCompareTo )) {
            return false;
        }
                                 
        //
        // check if the user is a team member and if he has rights to see this project
        //
        // we need to allow the projects which are parents of the project the user has rights on
        // to let the user see the proper position of the project in the tree
        $ids = $this->getAllChildrenIds($node['id']);
        $found = false;
        global $projectMember;
        // check if the user is a member of exactly this project
        $found = $projectMember->isMember($node['id']);

        if (!$found && sizeof($ids)) {
            if (sizeof($memProj = $projectMember->getMemberProjects())) {
                foreach ($memProj as $aProj) {
                    // intersect the arrays to see if any of $ids are in $memProj
                    // so we know if the user has any rights on any of the children projects of the current one
                    if(in_array( $aProj['projectTree_id'] , $ids )) {
                        $found = true;
                        break;
                    }
                }
            }
        }

        if (!$found) {
            return false;
        }


        return true;
    }

    /**
    *   check if the project given by $node is already closed
    *   for $timeToCompareTo
    *
    *   @param  mixed   array - one row of projectTree
    *                   int - the id of a row, needs to be retreived from the DB
    *   @param  int     if given this is the time used to compare the availability to
    *                   if not given the current time is used
    *   @return boolean true if project is already closed, false otherwise
    */
    function isClosed( $node , $timeToCompareTo )
    {
        if( !is_array($node) )
        {
            $node = $this->getElement($node);
        }
        if( $timeToCompareTo==null )
            $timeToCompareTo = time();

        // the projects are never closed during a current month or for any date that is in the future! 
        // that's definition :-)
        if( date('Ym') <= date('Ym',$timeToCompareTo) )
            return false;

        $parents = $this->getParents( $node['id'] );
        $parents = array_reverse($parents);
        $numDays = 0;
        foreach( $parents as $aParent )
        {
            if( $aParent['close'] )
            {
                $numDays = $aParent['close'];
                break;
            }
        }

        require_once('Date/Calc.php');

        // this is the last possible date when booking was allowed on entries for the previous month(s)
        $_date = explode('.',Date_Calc::endOfPrevMonth('','','','%d.%m.%Y'));
        $lastPossibleDate = mktime( 23 , 59 , 59 , $_date[1] , $_date[0] , $_date[2] );
        // go thru all the days after the last day of last month to find all weekend days and
        // holidays, which will be added to numDays, since they are no working days
        $x = $numDays;  // so it doesnt influence the loop when we modify $numDays
		$i=0;  // AK : be clean 
        while( $i<$x )
        {
            // increment first since the $lastPossibleDate is really the last second of the day, and so we can do the check below
            $i++;
            $checkDate = $lastPossibleDate + ($i*24*60*60);

// FIXXME check holidays tooo!!!
            if( date('w',$checkDate)==0 || date('w',$checkDate)==6) // check that it is no weekend day
            {
                $x++;   // for each weekend day we need to stay one more time in the loop :-)
            }
        }

        // the last date is the last one the loop had found
        $lastPossibleDate = $lastPossibleDate + ($i*24*60*60);

        if( $lastPossibleDate < time() )
            return true;

        return false;
    }

    /**
    *   this is only for the getAllVisible it is called by the walk-method
    *   to retreive only the nodes that shall be visible
    *
    *   @param      array   this is the node to check
    *   @return     mixed   an array if the node shall be visible
    *                       nothing if the node shall not be shown
    */
    function _walkForGettingVisibleFolders( $node )
    {
        global $session;

        if( $node['id']==$this->getRootId() )
        {
            if( isset($this->_unfoldAll) )                 // save the root folder too
                $session->temp->openProjectFolders[$node['id']] = $node['id'];
            return $node;
        }

        $parentsIds = $this->getParentsIds($node['id']);
        if( !isset($this->_unfoldAll) )
        {
            foreach( $parentsIds as $aParentId )
            {
                if( !isset($session->temp->openProjectFolders[$aParentId]) &&
                    @$aParentId!=$node['id'])    // dont check the node itself, since we only look if the parents are openend, then this $node is shown!
                    return false;
                    // AK added @ above as key id might not exist; no harm done then
            }
        }
        else
        {
            // if all folders shall be unfolded save the unfold-ids in the session
            $session->temp->openProjectFolders[$node['id']] = $node['id'];
        }
        return $node;
    }

    /**
    *   this returns all the visible projects, the folders returned
    *   are those which are unfolded, the explorer-like way
    *   it also handles the 'unfold' parameter, which we simply might be given
    *   so the unfold/fold works on every page that shows only visible folders
    *   i think that is really cool :-)
    *
    *   @return     array   only those folders which are visible
    */
    function getAllVisible()
    {
        $this->unfoldHandler();
        return $this->walk( array(&$this,'_walkForGettingVisibleFolders') , 0 , 'ifArray' );
    }

    /**
    *   this handles the REQUEST data that are responsible for the folding stuff
    *
    *   @input  $_REQUEST   array   global data
    */
    function unfoldHandler()
    {
        global $session;

        if( isset($_REQUEST['unfoldAll']) )
        {
            $this->_unfoldAll = true;
        }

        if( isset($_REQUEST['unfold']) )
        {
            if( isset($session->temp->openProjectFolders[$_REQUEST['unfold']]) )
            {
                unset($session->temp->openProjectFolders[$_REQUEST['unfold']]);
            }
            else
            {
                $session->temp->openProjectFolders[$_REQUEST['unfold']] = $_REQUEST['unfold'];
            }
        }
    }

                             
    /**
    *   check before updating data
    *   especially if any times are saved outside the period that shall be 
    *   set, if a period shall be set
    *   and probably many more things :-)
    */
    function checkBeforeUpdate($data)
    {                               
        global $applError;

        $time = new modules_time();
        $time->preset();                  
        $where = array();
        if( $data['startDate'] )    $where[] =  'timestamp<'.$data['startDate'];
        if( $data['endDate'] )      $where[] =  'timestamp>'.$data['endDate'];
        if (!sizeof($where)) {
            // tell the cache that the project-data have changed
            modules_project_cache::setModifiedByProject($data['id']);
            return true;
        }

        $time->setWhere( '('.implode(' OR ',$where).')' );
        $time->addWhere( 'projectTree_id='.$data['id'] );
        if ($count = $time->getCount()) {
            $applError->setOnce("There are already $count entries saved outside the new period, you want to specify!");
            return false;
        }             
        // tell the cache that the project-data have changed
        modules_project_cache::setModifiedByProject($data['id']);
        return true;
    }
    
    /**
    *   we override this method only to set the flag in the cache
    *
    */
    function add($data,$parentId)
    {
        $newId = parent::add($data,$parentId);
        modules_project_cache::setModifiedByProject($newId);
        return $newId;
    }
        
}

?>
