<?php
//
//  $Log: Navigation.php,v $
//  Revision 1.3  2003/03/11 12:57:55  wk
//  *** empty log message ***
//
//  Revision 1.3  2003/01/29 09:59:25  wk
//  - resolve some E_ALL issues
//
//  Revision 1.2  2002/10/24 14:17:56  wk
//  - added braces
//
//  Revision 1.1  2002/10/21 18:28:40  wk
//  - initial commit
//
//

require_once('Tree/Memory.php');

/**
*   this class handles a common navigation, which is saved in an Array
*   as defined by the PEAR::Tree module
*   it extends the Tree_Memory class since this tree is always needed completely
*   because the navigation is always visible, so all the items need to be seen
*
*   @package    modules
*   @version    2002/09/17
*   @access     public
*   @author     Wolfram Kriesing <wolfram@kriesing.de>
*/
class vp_Application_HTML_Navigation extends Tree_Memory
{

    function vp_Application_HTML_Navigation( $naviItems , &$configObject )
    {
        $this->_config = $configObject;
        parent::Tree_Memory('Array',array('children'=>$naviItems,'id'=>'root'));
        $this->setup();
    }
                      
    /**
    *   get me all the navi items from the tree and set handle
    *   if it shall be shown etc. this is done by the _naviArrayWalk
    *
    */
    function getAll()
    {        
        // setup the tree, so it is initialized and readable                         
        $this->setup();

/*  somehow like this it gotta work for unfoilding the navi items ... 
    watch out that the tree-handler uses getElement to get the elements!!!


        require_once('vp/Application/HTML/Tree.php');
        $treeHandler = new vp_Application_HTML_Tree();
        $unfolded = &$session->temp->navi->openFolders;
        if( !sizeof($unfolded) )
        {
            //$_REQUEST['unfoldAll'] = true;
            $rootId = $this->getRootId();
            foreach( $this->getChildren($rootId) as $aChild )
                $unfolded[] = $aChild['id'];
        }

        $naviItems = $treeHandler->getAllVisible( $this , $session->temp->navi->openFolders );
*/

        $this->_hideNodes = array();
        $naviItems = $this->walk( array(&$this,'_arrayWalk') , 0 , 'ifArray' );
        array_shift($naviItems);    // remove root element, since we dont need it
        return $naviItems;
    }
                                  
    /**
    *   this function is called by 'walk' to go through each 
    *   tree-node so we can transform, change or/and modify it as needed
    *   for the navigation
    *
    *   @param  array   this is the current element
    *   @return mixed   what to return is defined by the tree class,
    *                   so we have to return an array (the current element)
    *                   if it shall be visible in the tree,
    *                   or false if the current node shall not be visible in the tree
    */
    function _arrayWalk( &$element )
    {
        $parentId = $this->getParentId($element['id']);

        if( !in_array($parentId,$this->_hideNodes) &&
            (!isset($element['condition']) || $element['condition'] == true) )
        {               
            // check if the current node is the one selected in the navi
            if( @$element['url'] &&
                strpos($this->_config->applPathPrefix.$element['url'],$_SERVER['PHP_SELF']) === 0 )
            {
                $element['selected'] = true;
            }

            // do only complete the url if it doesnt contain '://' so it is not an absolute url, like 'http://www.google.de'
            if( @$element['url'] && strpos($element['url'],'://')===false )
            {
// FIXXME make the url relative so we can also use trans-sid one fine day
                $element['url'] = $this->_config->applPathPrefix.$element['url'];
            }
                       
            // this prevents from translation to work properly !!! better build the link by hand in the tpl!
            if( @$element['onClick'] )
            {
                $element['_link'] = '<a href="javascript://" class="navi" onClick="'.$element['onClick'].'">'.$element['name'].'</a>';
            }
            if( @$element['url'] )
                $element['_link'] = '<a href="'.$element['url'].'" class="navi">'.$element['name'].'</a>';

            // the root level is dismissed, so subtract one from each level
            $element['level']--;
            return $element;
        }
        $this->_hideNodes[] = $element['id'];
        return false;
    }

}
?>
