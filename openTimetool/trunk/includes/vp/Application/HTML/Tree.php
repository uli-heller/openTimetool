<?php
//
//  $Log: Tree.php,v $
//  Revision 1.3  2003/03/11 12:57:55  wk
//  *** empty log message ***
//
//  Revision 1.6  2002/10/17 14:42:53  wk
//  - added tree-handler for handling unfold etc.
//
//  Revision 1.5  2002/09/23 09:37:25  wk
//  - added PEAR-require
//  - use // instead of #
//
//  Revision 1.4  2002/09/11 15:57:27  wk
//  - added lastAction and lastId stuff
//  - return PEAR-errors
//
//  Revision 1.3  2002/07/25 10:07:43  wk
//  - use object as a reference
//  - if 'add' is set explicitly do it
//
//  Revision 1.2  2002/07/12 08:19:27  wk
//  - made move also work with 'src_ids'
//
//  Revision 1.1  2002/07/05 17:59:12  wk
//  - initial commit
//
//

require_once('PEAR.php');

/**
*
*
*   @package
*   @version    2002/06/05
*   @access     public
*   @author     Wolfram Kriesing <wolfram@kriesing.de>
*/
class vp_Application_HTML_Tree
{

    /**
    *   the last id that was worked on
    */
    var $_lastId = 0;

    /**
    *   the last action done, can be move, update, remove, etc.
    */
    var $_lastAction = '';

    /**
    *   Handle actions that modify a tree.
    *   This method takes data in arrays named by the action to take place
    *   i.e. $data['add']['name'] contains the name of the element to add in the tree
    *   inside this array you can define all the values that can be saved in the
    *   DB for this tree, i.e. if you add $data['add']['parent_id'] then the
    *   element will be added under the element with the id 'parent_id'.
    *   The array contains the following data
    *       $data[<action>][<columnname>]
    *   where
    *       <action>        is either 'add', 'move', 'update', 'remove'
    *       <columnname>    is the name of the column as defined in the DB
    *   If you additionally give the array $data['action'][<action>]
    *   then the method will right away know which action to proceed with
    *   this is reasonable if you use a form where the submit button has the
    *   name of the action, such as $data['action']['move'].
    *   If the action array is not given the method tries to determine
    *   the proper action to execute, depending on the data given.
    *   For the 'move' action the src and dest have to be given as follows:
    *       $data['move']['src_id'] and $data['move']['dest_id']
    *   or if multiple tree-elements shall be moved, 'src_ids' is an array
    *       $data['move']['src_ids'] and $data['move']['dest_id']
    *
    *   @version    2002/06/05
    *   @access     public
    *   @param      array       the data in arrays as described above
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    */
// FIXXME call setup only when working with Memory trees!!!
    function handle( $requestData , &$treeObject )
    {
//print_r($requestData);
        if( !$requestData )
        {
            return null;
        }

// FIXXME throw an error when $treeObject is no object !!!
        $parentColName = $treeObject->getOption('columnNameMaps','parentId');

        // if no definite action is given, try to find for which action all the
        // necessary data are given to execute it, if one was found
        // the action will be set
        // no definite action might be given if i.e. a remove-id is passed only
        // via a link, or if the user didnt push the button but stroke the enter key
        // but a given action always goes first!!!
        if( !sizeof($requestData['action']) )
        {
            if(vp_Application_HTML_Tree::_checkData($requestData['add'],$parentColName,1))
            {
                $requestData['action']['add'] = true;
            }
            if(vp_Application_HTML_Tree::_checkData($requestData['update'],'id',1))
            {
                $requestData['action']['update'] = true;
            }
            if(vp_Application_HTML_Tree::_checkData($requestData['remove'],'id'))
            {
                $requestData['action']['remove'] = true;
            }
            if( vp_Application_HTML_Tree::_checkData($requestData['move'],'src_id',1) ||
                vp_Application_HTML_Tree::_checkData($requestData['move'],'src_ids',1)
                )
            {
                $requestData['action']['move'] = true;
            }
        }

        if( isset($requestData['action']['add']) )
        {
//print "action add<br>";
            // get the parentId seperatly and remove it from the actual data
            $parentId = $requestData['add'][$parentColName];
            unset($requestData['add'][$parentColName]);

// FIXXME do error handling here!!!
            $res = $treeObject->add( $requestData['add'] , $parentId );
            if(!PEAR::isError($res)) {       
                $this->_lastId = $res;
            }
            $this->_lastAction = 'add';
            $treeObject->setup();
        }

        if( isset($requestData['action']['update']) &&
            vp_Application_HTML_Tree::_checkData($requestData['update'],'id',1))
        {
//print "action update<br>";
            $elementId = $requestData['update']['id'];
            unset($requestData['update']['id']);
// FIXXME do error handling here!!!
            $res = $treeObject->update( $elementId , $requestData['update'] );
            $this->_lastId = $elementId;
            $this->_lastAction = 'update';
            $treeObject->setup();
        }

        if( isset($requestData['action']['move']) &&
            (vp_Application_HTML_Tree::_checkData($requestData['move'],'src_id',1) ||
            vp_Application_HTML_Tree::_checkData($requestData['move'],'src_ids',1))
          )
        {
//print "action move<br>";
// FIXXME do error handling here!!!
            // check if src_ids is given, if so use it
            if( isset( $requestData['move']['src_ids']) && sizeof($requestData['move']['src_ids']) > 0 )
                $srcId = $requestData['move']['src_ids'];
            else
                $srcId = $requestData['move']['src_id'];

            if( isset($requestData['move']['prev_id']) )   // if a previous id is given move it under there
            {
                $res = $treeObject->move($srcId,0,$requestData['move']['prev_id']);
                $this->_lastId = $srcId;
                $this->_lastAction = 'move';
            }
            else
            {
                $res = $treeObject->move($srcId,$requestData['move']['dest_id']);
                $this->_lastId = $srcId;
                $this->_lastAction = 'move';
            }
            $treeObject->setup();
        }

        if( isset($requestData['action']['remove']) &&
            vp_Application_HTML_Tree::_checkData($requestData['remove'],'id') )
        {
//print "action remove<br>";
// FIXXME do error handling here!!!
            $res = $treeObject->remove($requestData['remove']['id']);
            $this->_lastId = $requestData['remove']['id'];
            $this->_lastAction = 'remove';
            $treeObject->setup();
        }
                               
        if( PEAR::isError( $res ) )
            return false;
        else
            return true;
    }

    /**
    *   this returns the last id(s) the handle method worked on
    *   so we dont need to know if it was update/add or whatever
    */
    function getLastId()
    {
        return $this->_lastId;
    }
    /**
    *   the last action that was executed
    *   @see    _lastAction
    */
    function getLastAction()
    {
        return $this->_lastAction;
    }

    /**
    *   this method goes through an array and checks if the requiredFields have values
    *
    *   @version    2002/06/05
    *   @access     public
    *   @param      array       the data to be checked
    *   @param      array       the strings of the required field names
    *   @param      int         the minimum size of data
    *                           besides the required fields
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    */
    function _checkData($data,$requiredFields,$minSize=0)
    {
        settype($requiredFields,'array');
        if( sizeof($data) )
        {
            foreach( $data as $key=>$aData )        // remove empty array elements
            {
                if( !$aData )
                {
                    unset($data[$key]);
                }
            }
        }
        if( sizeof($data) )
        {
            $foundReqFields = 0;
            foreach( $requiredFields as $aRequiredField)    // search if all required fields are given
            {
                if( $data[$aRequiredField] )
                {
                    $foundReqFields++;
                }
            }
        }
        if( sizeof($data) && $foundReqFields==sizeof($requiredFields) )
        {
            if( $minSize>0 && sizeof($data)<($minSize+$foundReqFields) )
                return false;
            return true;
        }
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
        if( $node['id']==$this->_treeObject->getRootId() )
        {
            if( $this->_unfoldAll )                 // save the root folder too
                $this->_openFoldersArray[$node['id']] = $node['id'];
            return $node;
        }

        $parentsIds = $this->_treeObject->getParentsIds($node['id']);
        if( !$this->_unfoldAll )
        {
            foreach( $parentsIds as $aParentId )
            {
                if( !$this->_openFoldersArray[$aParentId] &&
                    $aParentId!=$node['id'])    // dont check the node itself, since we only look if the parents are openend, then this $node is shown!
                    return false;
            }
        }
        else
        {
            // if all folders shall be unfolded save the unfold-ids in the session
            $this->_openFoldersArray[$node['id']] = $node['id'];
        }
        return $node;
    }

    /**
    *   this returns all the visible nodes, the folders returned
    *   are those which are unfolded, the explorer-like way
    *   it also handles the 'unfold' parameter, which we simply might be given
    *   so the unfold/fold works on every page that shows only visible folders
    *   i think that is really cool :-)
    *                       
    *   @param      array   the array that contains all open folder ids
    *   @return     array   only those folders which are visible
    */
    function getAllVisible( &$treeObject , &$openFoldersArray )
    {                                   
// FIXXME may be make a method setTreeObject        
        $this->_treeObject = &$treeObject;
        $this->_openFoldersArray = &$openFoldersArray;

        $this->unfoldHandler( $openFoldersArray );
        return $treeObject->walk( array(&$this,'_walkForGettingVisibleFolders') , 0 , 'ifArray' );
    }

    /**
    *   this handles the REQUEST data that are responsible for the folding stuff
    *
    *   @param      array   the array that contains all open folder ids
    *   @input  $_REQUEST   array   global data
    */
    function unfoldHandler( &$openFoldersArray )
    {
        if( $_REQUEST['unfoldAll'] )
        {
            $this->_unfoldAll = true;
        }

        if( $_REQUEST['unfold'] )
        {
            if( $openFoldersArray[$_REQUEST['unfold']] )
            {
                unset($openFoldersArray[$_REQUEST['unfold']]);
            }
            else
            {
                $openFoldersArray[$_REQUEST['unfold']] = $_REQUEST['unfold'];
            }
        }
    }


}   // end of class

?>
