<?php
/**
 * 
 * $Id$
 * 
 *
 *   @package
 *   @version    2002/07/17
 *   @access     public
 *   @author     Wolfram Kriesing <wolfram@kriesing.de>
 */
class vp_Application_HTML_NextPrev
{
    var $_object = null;

    var $_text = array(
	'en' => array(
		'Datasets {$this->listStartPlusOne}…{$this->lastRowNumber}'
                    . ' of {$this->count}&nbsp;&middot;&nbsp;',
		' Datasets visible (at most)',
	),
	'de' => array(
		'Datens&auml;tze {$this->listStartPlusOne}…{$this->lastRowNumber}'
                    . ' von {$this->count}&nbsp;&middot;&nbsp;',
		' Datens&auml;tze anzeigen (max.)',
	),
    );

    /**
     *   those are the default counts available
     */
    var $_availCounts = array(5, 10, 20, 50, 75, 100, 200);

    var $lang = 'en';


    function getText($index)
    {
    	$ret = ''; // AK : just to be clean
        $this->listStartPlusOne = $this->listStart + 1;
        eval("\$ret = \"" . $this->_text[$this->lang][$index] . "\";");
        return $ret;
    }

    function setLanguage($lang = 'en')
    {
        $this->lang = $lang;
    }

    /**
     *   handles some next-prev settings
     *
     *   @version    2002/07/17
     *   @access     public
     *   @param      object      a vp_DB_Common instance that this next-prev logic works on
     *   @param      array       contains strings which are the names of variables
     *                           that need to be passed on via teh REQUEST (get in this case)
     *   @author     Wolfram Kriesing <wolfram@kriesing.de>
     */
//    function vp_Application_HTML_NextPrev($object, $count = null)
    function vp_Application_HTML_NextPrev(&$object, $addData = array(), $defaultListCount = 10)
    {
//        global ${$this->persistentClass};
//        $session = &${$this->persistentClass};
// FIXXME THIS IS NOT COOOL, use persistentClass, but i had problems with that :-(
        global $session;

        $this->_object = &$object;
        
        // AK : added as next stmnt seems to be a trial relict;
        // see commented function call above
	$count = null;
        if ($count === null) {
            $count = $this->_object->getCount();
        }

        if (!isset($session->temp->nextPrev)) {
            $session->temp->nextPrev = new stdClass();
        }
        if (isset($_REQUEST['setListCount'])) {
            $session->temp->nextPrev->listCount = (int) $_REQUEST['setListCount'];
        }
        if (empty($session->temp->nextPrev->listCount) ||
		!in_array($session->temp->nextPrev->listCount, $this->_availCounts)) {
            $session->temp->nextPrev->listCount = $defaultListCount;
        }

        if (isset($_REQUEST['setListStart'])) { // AK : isset
            $this->listStart = (int) $_REQUEST['setListStart'] < 0
                             ? 0 : (int) $_REQUEST['setListStart'];
        }
        if (!isset($this->listStart)) { // AK : isset
            $this->listStart = 0;
        }

        // correct it by 1 since $this->listStart starts at 1
        $this->lastRowNumber = $session->temp->nextPrev->listCount + $this->listStart;
        $this->lastRowNumber = $this->lastRowNumber>$count ? $count : $this->lastRowNumber;

        if ($count > $this->lastRowNumber) {
            $this->showNext = true;
        }

        if ($this->listStart > 1) {
            $this->showPrev = true;
        }

        $this->count = $count;
        // build the url prefix so we submit the data that this page may be requires
        // to show the data properly or to get the data properly from the db etc.
        // i.e. if you show only a special subset of data and pass this parameter via the REQUEST
        // which might be either post or get
        $this->urlPrefix = $_SERVER['PHP_SELF'] . '?';
        settype($addData, 'array');
        if (sizeof($addData)) {
            foreach ($addData as $aData) {
                $this->urlPrefix .= $aData . '=' . urlencode($_REQUEST[$aData]) . '&';
            }
        }

        $this->prevLink = $this->urlPrefix . 'setListStart='
                        . ($this->listStart - $session->temp->nextPrev->listCount);
        $this->nextLink = $this->urlPrefix . 'setListStart='
                        . ($this->listStart + $session->temp->nextPrev->listCount);

        $this->beginLink = $this->urlPrefix . 'setListStart=0';
        $this->endLink = $this->urlPrefix . 'setListStart='
                       . (floor($this->count/$session->temp->nextPrev->listCount) *
                                $session->temp->nextPrev->listCount);
    }

    /**
     *   use this method to retreive the data as it would be done the
     *   default way
     * 
     *   @return     array   returns the data getAll returns
     */
    function getData()
    {
        global $session;

        return $this->_object->getAll($this->listStart, $session->temp->nextPrev->listCount);
    }

} // end of class
