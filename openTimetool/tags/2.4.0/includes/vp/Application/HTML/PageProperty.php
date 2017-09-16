<?php
/**
 * 
 * $Id$
 * 
 */

require_once 'Tree/Memory.php';

/**
 *   this class can be used to set certain properties for a page
 *   and any of its subpages
 *   i.e. you can say that all pages under /modules/about
 *   shall have the following properties:
 *       'pageHeader', 'author', etc...
 *   by calling 'get' you get the property given for the current page
 *   this way you can create certain settings or options for each page
 *   which you can use on this page by simply calling 'get'  
 *
 *   usage example:
 *    $properties = array(
 *                    'modules/time/summary'      =>  array('pageHeader'=>'Summary')
 *                    ,'modules/time/multi'       =>  array('pageHeader'=>'Multi-Log')
 *                    ,'modules/time/quick'       =>  array('pageHeader'=>'Quick-Log')
 *                    );
 *
 *    $pageProp = new vp_Application_HTML_PageProperty( $properties , $config )
 *
 *
 *   @package    modules
 *   @version    2002/10/21
 *   @access     public
 *   @author     Wolfram Kriesing <wolfram@kriesing.de>
 */
class vp_Application_HTML_PageProperty
{

    function vp_Application_HTML_PageProperty(&$properties, &$configObject)
    {
        $this->_props = $properties;
        $this->_config = $configObject;
    }

    function set($which, $properties)
    {
        // if the value doesnt exist yet, add it in front to be sure this is used before the others that 
        // are already in the array, since we assume, if one uses set this applies first
        if (!isset($this->_props[$which])) { // AK : isset
            $this->_props = array_merge(array($which => $properties), $this->_props);
        } else {
            $this->_props[$which] = $properties;
        }
    }

    /**
    *   get a certain property for the current page
    *   @param  string  the property to retreive for the current page
    *   @return mixed   returns the requested property
    */
    function get($which = null)
    {
        // do only remove the prefix, if it is infront, in live mode the applPathPrefix is empty
        // so we only want to remove the first / , so we use substr here, not str_replace, that would replace all / :-)
        $myPrefix = $this->_config->applPathPrefix . '/';
        if (strpos($_SERVER['PHP_SELF'], $myPrefix ) === 0) {
            $_curItem = substr($_SERVER['PHP_SELF'], strlen($myPrefix));
        }

        $_curItem = str_replace('//', '/', $_curItem);
        foreach ($this->_props as $pageUrl => $aProperty) {
            if (strpos($_curItem, $pageUrl) === 0) {
                return @$aProperty[$which];
            }
        }
        // no property found for this page
        return null;
    }

}
