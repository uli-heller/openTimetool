<?php
//
//  $Id
//
//  Revision 1.2.2.5  2006/08/26 		   AK
//  - fixed getInstance() : $projectTree is global and needs check to avoid notices
//
//  Revision 1.2.2.4  2003/04/10 19:00:34  wk
//  - fixed getInstance()
//
//  Revision 1.2.2.3  2003/04/10 18:04:32  wk
//  - use references for getInstance!
//
//  Revision 1.2.2.2  2003/03/28 10:06:43  wk
//  - add param maxLength to getPathAsString()
//
//  Revision 1.2.2.1  2003/03/17 09:23:34  wk
//  - getPathAsString pays respect to the wasModifed() now
//
//  Revision 1.2  2003/03/10 19:27:15  wk
//  - added getInstance
//
//  Revision 1.1  2003/02/10 18:46:05  wk
//  - initial commit, still needs a lot of work
//
//

require_once 'Tree/Dynamic/DBnested.php';
require_once $config->classPath.'/modules/project/member.php';
require_once $config->classPath.'/modules/project/cache.php';
require_once $config->classPath.'/modules/time/time.php';

class modules_project_treeDyn extends Tree_Dynamic_DBnested
{


    function &getInstance()
    {
        global $projectTreeDyn,$config,$projectTree;  //AK : I think $projectTree is global as well ..

        // is projectTree an instance of this class?
        // A.K. : check if projectTree is defined 
//        if (isset($projectTreeDyn)) {
	
			// AK : Difference between php4 & php5 : in php4 the classname was always in lower case
	        // Toni, SX : get_class changed its behaviour slightly in php 5.3
    	    // we now have additionaly to check if projectTree is existing 
        	// otherwise no tree is every created. This is better anway and should work on any php version			
        	if (empty($projectTreeDyn) || strtolower(get_class($projectTree))!=strtolower(__CLASS__)) {
            	$treeOptions = array(
                                'table'         =>  TABLE_PROJECTTREE,
                                'order'         =>  'name'
                	            );
            	$projectTreeDyn =& new modules_project_treeDyn( $config->dbDSN , $treeOptions );
        	}
//        }
        return $projectTreeDyn;
    }

    var $_checkedIfModified = false;
    
    /**
    *   this method retreives the path as a string, it caches the results too
    *
    *   @param  int     the tree-node to ge the path for
    *   @param  int     the maxLength of the project name, this will cut the name
    *                   short and add '...' in the beginning
    *   @return string  the path, each node seperated by |
    */
    function getPathAsString($id,$maxLength=0)
    {
        global $session,$userAuth;

        // did we already check if the 
        if (!$this->_checkedIfModified) {
            if (modules_project_cache::wasModified($userAuth->getData('id'),'Overview_byProject')) {
                $session->projectTreeDyn->pathsAsString = array();
            }
            $this->_checkedIfModified = true;
        }

        if (!isset($session->projectTreeDyn) || !isset($session->projectTreeDyn->pathsAsString[$id])) {
            $session->projectTreeDyn->pathsAsString[$id] = parent::getPathAsString($id,' | ',1);
        } 
        $ret = $session->projectTreeDyn->pathsAsString[$id];
        if (is_numeric($maxLength) && $maxLength>0 && strlen($ret)>$maxLength) {
            $ret = '...'.substr($ret,-$maxLength);
        }
        return $ret;
    }

    /**
    *   just a wrapper to be compatible to DB_QueryTool
    *
    */
/*    function &getAll()
    {
        return $this->getNode();
    }
*/
    /**
    *   get only the projects which's startdate-enddate period
    *   is valid, all the active projects only
    *
    */
/*
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
*/
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
/*    function isAvailable( $node , $timeToCompareTo )
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
        // check if the user is a member of exactly of this project
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
/*    function isClosed( $node , $timeToCompareTo )
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
/*    function _walkForGettingVisibleFolders( $node )
    {
        global $session;

        if( $node['id']==$this->getRootId() )
        {
            if( $this->_unfoldAll )                 // save the root folder too
                $session->temp->openProjectFolders[$node['id']] = $node['id'];
            return $node;
        }

        $parentsIds = $this->getParentsIds($node['id']);
        if( !$this->_unfoldAll )
        {
            foreach( $parentsIds as $aParentId )
            {
                if( !$session->temp->openProjectFolders[$aParentId] &&
                    $aParentId!=$node['id'])    // dont check the node itself, since we only look if the parents are openend, then this $node is shown!
                    return false;
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
/*    function getAllVisible()
    {
        $this->unfoldHandler();
        return $this->walk( array(&$this,'_walkForGettingVisibleFolders') , 0 , 'ifArray' );
    }

    /**
    *   this handles the REQUEST data that are responsible for the folding stuff
    *
    *   @input  $_REQUEST   array   global data
    */
/*    function unfoldHandler()
    {
        global $session;

        if( $_REQUEST['unfoldAll'] )
        {
            $this->_unfoldAll = true;
        }

        if( $_REQUEST['unfold'] )
        {
            if( $session->temp->openProjectFolders[$_REQUEST['unfold']] )
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
/*    function checkBeforeUpdate( $data )
    {
        global $applError;

        $time = new modules_time();
        $time->preset();
        $where = array();
        if( $data['startDate'] )    $where[] =  'timestamp<'.$data['startDate'];
        if( $data['endDate'] )      $where[] =  'timestamp>'.$data['endDate'];
        if( !sizeof($where) )
            return true;

        $time->setWhere( '('.implode(' OR ',$where).')' );
        $time->addWhere( 'projectTree_id='.$data['id'] );
        if( $count = $time->getCount() )
        {
            $applError->setOnce("There are already $count entries saved outside the new period, you want to specify!");
            return false;
        }
        return true;
    }
*/
}

$projectTreeDyn =& modules_project_treeDyn::getInstance();

?>
