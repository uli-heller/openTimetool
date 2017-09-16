<?php
/**
 * 
 * $Id$
 * 
 */

require_once 'vp/DB/Common.php';

/**
 *   This class handles global user specific settings.
 *   It requires a special setup of 2 tables in the DB.
 *   see the examples.
 * 
 *   @package  proxy
 *   @access   public
 *   @author   Wolfram Kriesing <wolfram@kriesing.de>
 *   @version  sometime in 2000 :-)
 */
class vp_Application_Setting extends vp_DB_Common
{

    var $settingTable = TABLE_SETTING;
    // this is the table the vp_DB_Common works on
    var $table = TABLE_CODEMASTER;

    var $mainType = '';

    // the vp_DB_Common object, which works on the setting table
    var $_setting = null;


    /**
     *   Constructor
     * 
     *   array    contains all the settings temporarily, so we only need to get them once from the DB
     *   @access private
     *   @see    get()
     */
    var $_data = NULL;

    function __construct(&$db, &$error)
    {
        parent::vp_DB_Common($db, $error);
        $this->_setting = new vp_DB_Common($db, $error);
        $this->_setting->setTable($this->settingTable);
    }

    /**
     *   gets all the current settings from the db
     *   and saves them in $this->_data
     *
     *   @version    2001/05/11, 2002/02 complete rework
     *   @author     Wolfram Kriesing <wolfram@kriesing.de>
     *   @param      string  $subtype this is the column subtype in the codeMaster-table
     *   @return
     */
    function getAll($addJoinWhere = '')
    {
        //
        //  get only the codemaster data, to be sure to have all the available settings
        //
        $_temp = new vp_DB_Common($this->_db, $this->_error);
        $_temp->setTable($this->table);
        $_temp->setWhere('maintype=' . $this->_db->quote($this->mainType));
        if ($codemasters = $_temp->getAll()) {
            foreach ($codemasters as $aSetting) {
                if ($aSetting['subtype1']) {
                    $data[$aSetting['subtype']][$aSetting['subtype1']]['codemaster_id'] = $aSetting['id'];
                    if ($aSetting['type']) {
                        $data[$aSetting['subtype']][$aSetting['subtype1']]['type'] = $aSetting['type'];
                    }
                    if ($aSetting['comment']) {
                        $data[$aSetting['subtype']][$aSetting['subtype1']]['comment'] = $aSetting['comment'];
                    }
                } else {
                    $data[$aSetting['subtype']]['codemaster_id'] = $aSetting['id'];
                    if ($aSetting['type']) {
                        $data[$aSetting['subtype']]['type'] = $aSetting['type'];
                    }
                    if ($aSetting['comment']) {
                        $data[$aSetting['subtype']]['comment'] = $aSetting['comment'];
                    }
                }
            }
        }

        //
        // get the real data
        //
        $this->setLeftJoin($this->settingTable,
                $this->settingTable . '.codemaster_id=' . $this->table . '.' . $this->primaryCol);
        // do this after the 'setLeftJoin' !!!
        if ($addJoinWhere) {
            $this->addWhere($addJoinWhere);
        }

//print $this->_buildSelectQuery().'<br><br>';
        $this->addWhere('maintype=' . $this->_db->quote($this->mainType));
        $results = parent::getAll();

        if ($results) {
            $valueColName = '_' . $this->settingTable . '_value';
            $idColName = '_' . $this->settingTable . '_id';
            foreach ($results as $aSetting) {
                // if there is already a value for this subtype, then we have to create an array
                // of the values, if it isnt one yet
                if ($aSetting['subtype1']) {
                    $_tempVal = $data[$aSetting['subtype']][$aSetting['subtype1']]['value'];
                } else {
                    $_tempVal = $data[$aSetting['subtype']]['value'];
                }

                if (isset($_tempVal)) {
                    if (is_array($_tempVal)) {
                        array_push($_tempVal, $aSetting[$valueColName]);
                        $aSetting[$valueColName] = $_tempVal;
                    } else {
                        $aSetting[$valueColName] = array($_tempVal, $aSetting[$valueColName]);
                    }
                }

                if ($aSetting['subtype1']) {
                    $data[$aSetting['subtype']][$aSetting['subtype1']]['value'] = $aSetting[$valueColName];
                    $data[$aSetting['subtype']][$aSetting['subtype1']]['codemaster_id'] = $aSetting['id'];
                    $data[$aSetting['subtype']][$aSetting['subtype1']]['id'] = $aSetting[$idColName];
                    if ($aSetting['type']) {
                        $data[$aSetting['subtype']][$aSetting['subtype1']]['type'] = $aSetting['type'];
                    }
                    if ($aSetting['comment']) {
                        $data[$aSetting['subtype']][$aSetting['subtype1']]['comment'] = $aSetting['comment'];
                    }
                } else {
                    $data[$aSetting['subtype']]['value'] = $aSetting[$valueColName];
                    $data[$aSetting['subtype']]['codemaster_id'] = $aSetting['id'];
                    $data[$aSetting['subtype']]['id'] = $aSetting[$idColName];
                    if ($aSetting['type']) {
                        $data[$aSetting['subtype']]['type'] = $aSetting['type'];
                    }
                    if ($aSetting['comment']) {
                        $data[$aSetting['subtype']]['comment'] = $aSetting['comment'];
                    }
                }
            }
        }
        $this->_data = $data;

        return $data;
    } // end of function

    /**
     *   gets the setting(s) from the db and saves them in the private-property _data
     *   if they are not already in there
     *
     *   @version    2002/02/27
     *   @author     Wolfram Kriesing <wolfram@kriesing.de>
     *   @param      string  every argument is a settings-subtype
     *   @return     string  the value requested
     */
    function get()
    {	
     	$value = ''; // AK : eliminate some warnings

        if (!func_num_args()) {
            return NULL;
        }

        if (!is_array($this->_data)) {
            $this->_data = $this->getAll();
        }

        $args = func_get_args();
        // build the
        $reference = "this->_data['" . implode("']['", $args) . "']['value']";
        eval("\$value=\$$reference;");

        return $value;
    } // end of function

    /**
     *   gets the id for the given types
     *
     *   @version    2002/07/11
     *   @author     Wolfram Kriesing <wolfram@kriesing.de>
     *   @param      string  every argument is a settings-subtype
     *   @return     int     the codemaster id
     */
    function getId()
    {
        if (!func_num_args()) {
            return NULL;
        }
        $args = func_get_args();
        return $this->_getId($args);
    }

    /**
     *   gets the id that belongs to the given setting
     *
     *   @version    2002/03/24
     *   @author     Wolfram Kriesing <wolfram@kriesing.de>
     *   @param      array   the types
     *   @param      int     this value tells where the types start
     *                       0 means the first parameter is the first type
     *   @return     string  the codemaster id
     */
    function _getId($args, $startAt = 0)
    {
    	$id = ''; // AK : eliminate some warnings

        if (!is_array($this->_data)) {
            $this->_data = $this->getAll();
        }

        for ($i=0 ; $i < $startAt; $i++) {
            array_shift($args);
        }

        // build the
        $reference = "this->_data['" . implode("']['", $args) . "']['codemaster_id']";
        eval("\$id=\$$reference;");

        return $id;
    }

    /**
     *
     *
     *   @version    2002/02/27
     *
     *   @author     Wolfram Kriesing <wolfram@kriesing.de>
     *
     *   @param      integer $uid    the user id
     *   @param      string          every further argument is a settings-subtype
     *   @return
     *
     */
/*
    function getForUser($uid)
    {
        if (!$this->_dataByUser[$uid]) { // do only read the data again from db if we dont have them yet
            $this->_dataByUser[$uid] = $this->getAll($uid);
        }

        $args = func_get_args();
        array_shift($args); // remove the first element, since it is the uid
        // build the
        $reference = "this->_dataByUser[$uid]['" . implode("']['", $args) . "']['value']";
        eval("\$value=\$$reference;");

        return $value;
    }
*/
    /**
     *   saves the value in the DB and sets the value of this class's property
     *
     *   @version    2002/02/27
     *
     *   @author     Wolfram Kriesing <wolfram@kriesing.de>
     *
     *   @param      array   $newData    the new settings to be saved, the array is like this: codemaster_id=>value,codemaster_id=>value,...
     *   @param      integer $uid
     *   @return     boolean returns true for success
     *
     */
/*
    function save($newData, $uid = 0)
    {
        global $db, $user, $error;

        if ($uid == 0) {
            $uid = $this->getUid();
        }

        // remove the old settings values
        $query = sprintf('DELETE FROM %s WHERE uid=%s AND codemaster_id IN (%s)',
                          TABLE_SETTINGS, $uid,
                          implode(',', array_keys($newData)) // remove only those that will be written again
                        );
        if (DB::isError($res = $db->query($query))) {
            // do this properly
            $error->set('An error occured while saving the settings, please try again!');
            $error->log("settings::save $query " . $res->message);
            return false;
        }

        // add the new values as passed to this method
        $values = array();
        foreach ($newData as $codemasterId => $value) {
            if (!$value) { // lets leave out settings which are not set to any value, so we save space in the DB
                continue;
            }
 
            $valuePair = '('.$db->nextId(TABLE_SETTINGS);
            $valuePair.= ",$codemasterId,$uid,".$db->quote($value).')';
            $values[] = $valuePair;
        }
        $query = sprintf('INSERT INTO %s (id,codemaster_id,uid,value) VALUES %s',
                          TABLE_SETTINGS,
                          implode(',', $values));
        if (DB::isError($res = $db->query($query))) {
            // do this properly
            $error->set('An error occured while saving the settings, all your settings are lost, please set and save them again! Sorry.');
            $error->log("settings::save $query " . $res->message);
            return false;
        }

        // regenerate the private property '_data'
        $this->getAll();

        return true;
    } // end of function
*/
    /**
     *   updates one value
     *
     *   @version    2002/03/24
     *
     *   @author     Wolfram Kriesing <wolfram@kriesing.de>
     *
     *   @param      array   $newData    the new settings to be saved
     *   @param      integer $uid
     *   @return     boolean returns true for success
     *
     */
/*
    function saveOne($value)
    {
        global $user, $db, $error;

        if (!sizeof($this->_data)) { // if this->_data is not filled we do it here :-)
            $this->getAll();
        }

        $args = func_get_args();
        array_shift($args); // remove the first element, since it is the value
        $reference = "this->_data['".implode( "']['" , $args )."']['codemaster_id']";
        eval("\$key=\$$reference;");

        // the realUid is the uid of the currently logged in user !!!
        $uid = $this->getUid();
        $query = "DELETE FROM ".$this->settingTable." WHERE codemaster_id=$key AND uid=$uid";
        if (DB::isError($res = $db->query($query))) {
            // do this properly
            $error->set('An error occured while saving the settings, please try again!');
            $error->log("settings::saveOne $query " . $res->message);
            return false;
        }

        $id = $db->nextId($this->settingTable);
        $query = sprintf('INSERT INTO %s (id,value,codemaster_id,uid) VALUES (%s,%s,%s,%s)',
                          $this->settingTable ,
                          $id , $db->quote($value), $key, $uid);
        if (DB::isError($res = $db->query($query))) {
            // do this properly
            $error->set('An error occured while saving the setting, please try again! Sorry.');
            $error->log("settings::saveOne $query " . $res->message);
            return false;
        }

        // regenerate the private property '_data'
        $this->getAll();

        return true;
    }
*/
    function save($data)
    {
        if ($data['value']) {
            return $this->_setting->save($data);
        } else {
            unset($data['value']);
            $this->remove($data);
        }
    }

    /**
     *   add a setting in the setting table
     *
     *   @version    2002/07/05
     *
     *   @author     Wolfram Kriesing <wolfram@kriesing.de>
     *   @param      mixed   the value
     *   @param      string  all the following parameters give the type under which to save this value
     *   @return     boolean returns true for success
     *
     */
    function add($data)
    {
        if (!is_array($data)) {
            $tmp = $data;
            $data = array();
            $data['value'] = $tmp;
        }

        if (sizeof($args = func_get_args()) > 1) {
            $data['codemaster_id'] = $this->_getId($args, $startAt = 1);
        }

        $ret = $this->_setting->add($data);
        // update the internal array!!!
        $this->getAll();
        return $ret;
    }

    /**
     *   remove a setting in the setting table
     *
     *   @version    2002/07/05
     *
     *   @author     Wolfram Kriesing <wolfram@kriesing.de>
     *   @param      mixed   the value
     *   @param      string  all the following parameters give the type under which to save this value
     *   @return     boolean returns true for success
     *
     */
    function remove($values)
    {
        settype($values, 'array');

        if (sizeof($args = func_get_args()) > 1) {
            $values['codemaster_id'] = $this->_getId($args, $startAt = 1);
        }

        $ret = $this->_setting->remove($values);
        // update the internal array!!!
        $this->getAll();
        return $ret;
    }

    /**
     *   initalize standard settings for a (new) user
     *   be sure to overwrite this method if you need it
     *
     *   @version    2001/12/07
     *
     *   @abstract
     *   @author     Wolfram Kriesing <wolfram@kriesing.de>
     *   @param      int     $uid    user id
     *   @return     boolean returns true for success
     *
     */
    function init($uid)
    {
/*
        $this->save(array($this->getId( 'browsing','toolbar') => true,
                          $this->getId( 'browsing','bookmarking') => true),
                    $uid);
*/
    } // end of method

} // end of class
