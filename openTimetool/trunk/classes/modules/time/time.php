<?php
/**
 * 
 * $Id$
 * 
 */

require_once $config->classPath . '/modules/common.php';
require_once $config->classPath . '/modules/project/tree.php';
require_once $config->classPath . '/modules/project/member.php';

/**
 * 
 * 
 * @package modules
 * @access public
 * @author Wolfram Kriesing <wolfram@kriesing.de>
 */
class modules_time extends modules_common
{

    var $table = TABLE_TIME;

    //function debug($m){error_log($m);}

    /**
     *   set the default join and order for a new instance
     *
     *   @author     Wolfram Kriesing <wk@visionp.de>
     */
    function modules_time()
    {
        parent::modules_common();
        $this->preset();
    }

    /**
     *   this does a reset and sets the initial state as we think we mostly need it :-)
     */
    function preset()
    {
        $this->reset();
/*
        $this->setJoin(
            array(TABLE_PROJECTTREE, TABLE_TASK, TABLE_USER),
            TABLE_PROJECTTREE . '.id=' . TABLE_TIME . '.projectTree_id AND ' .
            TABLE_TASK . '.id=' . TABLE_TIME . '.task_id AND ' .
            TABLE_TIME . '.user_id=' . TABLE_USER . '.id'
        );
*/
        $this->autoJoin(array(TABLE_PROJECTTREE, TABLE_TASK, TABLE_USER));
        $this->setOrder('user_id,timestamp');
    }

    /**
     *   overwrite this method to check if the user is allowed to get the data
     * 
     */
    function get($id)
    {
        global $user, $userAuth, $applError;

        // if i.e. setSelect('timestamp') is used
        if (strpos($this->getSelect(), 'user_id') === false) {
            $this->addSelect('user_id');
        }
        $data = parent::get($id);
        // if this entry is not the user's own entry, check if he is admin
        // if not he is not allowed to add a new entry
        //var_dump($id); var_dump($data['user_id']);
        //var_dump($userAuth->getData('id')); var_dump($user->isAdmin());
        if (empty($data['user_id'])) {
            // in some strange situation I'll get a useless id; Seems to be the
            // id of a deleted entry in db.... dont know why, but I skip that
            // ToDo: seems it comes from my xajax enhancement; have to look in that later
            return false;
        }
        if ($data['user_id'] != $userAuth->getData('id') && !$user->isAdmin()) {
            $applError->set('You don\'t have the permission to edit this entry!');
            return false;
        }
        return $data;
    }

    /**
     *   overwrite save to convert the timestamp from human date to timestamp
     *   and check if the user is allowed to save the data
     *
     *   @author     Wolfram Kriesing <wk@visionp.de>
     *   @param      array   the data from the form
     *   @return     array   the converted data
     */
    function save($data)
    {
        global $applError, $util, $user, $userAuth;

        $curUserId = $userAuth->getData('id');
        // AK : was !$data['user_id']
        if (!isset($data['user_id']) || empty($data['user_id'])) {
            $data['user_id'] = $curUserId;
        }
        // convert timestamp
        // AK : added 2xisset to avoid notices
        if (isset($data['timestamp_time']) && isset($data['timestamp_date'])) {
            $date = array_map('intval', explode('.', $data['timestamp_date']));
            $time = array_map('intval', explode(':', $data['timestamp_time']));
            $data['timestamp'] = mktime(
                $time[0], $time[1], 0, $date[1], $date[0], $date[2]
            );
            unset($data['timestamp_time']);
            unset($data['timestamp_date']);
        }
        // check data a bit
        if ($data['timestamp'] == 0) {
            $applError->set('Please specify a valid time and date!');
            return false;
        }

        if (!$data['projectTree_id']) {
            $msg = 'Please specify a project!';
            $applError->setOnce($msg);
            return false;
        }

        //
        //  check if the user we are booking the time for is a member of the project
        //  where the time shall be booked onto
        //
        // since $projectMember caches data internally we only
        // need on instance throughout the entire application
        // actually we could also use it from global, but hey :-)
        $projectMember = modules_project_member::getInstance();

        $projectTree = modules_project_tree::getInstance();
        if (!$projectMember->isMember($data['projectTree_id'], $data['user_id'])) {
            $_project = $projectTree->getPathAsString($data['projectTree_id']);
            if ($data['user_id'] != $curUserId) {
                $userData = $user->get($data['user_id']);
                $applError->setOnce("{$userData['name']} {$userData['surname']}" .
                        " is not a team member of project: '$_project'!");
            } else {
                $applError->setOnce("You are not a team member of project: '$_project'!");
            }
            return false;
        }

        // check if the project is available after checking if the user is a member,
        // that's why this check is behind the one before :-)
//print 'save: $projectTree->isAvailable( '.$data['projectTree_id'].' , '.$data['timestamp'].' )<br>';
//echo gmdate("M d Y H:i:s", $data['timestamp']);
//print_r($data);echo "<p>";
        if (!$projectTree->isAvailable($data['projectTree_id'], $data['timestamp'])) {
            /* dont show the period because the project might also be closed
               for the month where the user wants to log a time for
               if we want to activate this again then we also have
               to check the closing date!
            $project = $projectTree->getElement($data['projectTree_id']);
            $from = $util->convertTimestamp($project['startDate']);
            $until = $util->convertTimestamp($project['endDate']);
            // in case we cant get the available-period dont show the dates,
            // this happens when a parent is not available
            if ($from || $until) {
                $msg = 'This project is only available ' . ($from?$from:'...')
                     . ' through ' . ($until?$until:'...') . '!';
            } else
            */
            $msg = 'This project is not available at the date you specified'
                 . ' or not modifyable anymore without admin permissions!';
            $applError->setOnce($msg);
            return false;
        }

        //echo "// we are after isAvail ...<br>";

        //
        //  prepare data and save
        //
        // get the old data, so we can update the duration later
        $oldData = array();
        // we dont need the joined data here, so we dont need to call preset
        $this->reset();
        // AK : eliminate php notice by isset
        if (isset($data['id']) && !empty($data['id'])) {
            $oldData = $this->get($data['id']);
        }

        // if this entry is not the user's own entry, check if he is admin
        // if not he is not allowed to add a new entry
        // AK : eliminated php noctice with @
        if (@$data['id'] && $oldData['user_id'] != $curUserId && !$user->isAdmin()) {
            $msg = 'You don\'t have the permission to edit this entry!';
            $applError->setOnce($msg);
            return false;
        }
        $ret = parent::save($data);
        if ($ret) {
            // this updates the column duration for the entry
            // passes either the new ID or the old data
            // AK eliminated php notice with isset
            if (isset($data['id'])) {
                // first update the entries around the old time
                $this->_updateDuration($oldData);
                // update the entries around the updated time
                $this->_updateDuration($data['id']);
            } else {
                $this->_updateDuration($ret);
            }
        }
        return $ret;
    }

    /**
     * Round the duration for a certain project.
     * Since 2.1 the rounding works that way, that the duration is rounded
     * to the higher value of the round-value, so i.e. 1min, 2min, or 13min
     * would get rounded to 15min when the rounding is set to 15min.
     * 
     * @param integer the actual duration calculated out of the
     *                 difference of timestamp, in seconds
     * @param integer the project ID
     * @param boolean true when a message shall be shown for the user
     */
    function roundTime($duration, $projectId)
    {
        global $dateTime, $applMessage;

        $projectTree = modules_project_tree::getInstance();
        $parents = $projectTree->getParents($projectId);
        $parents = array_reverse($parents);
        // AK initialTime not used here -> eliminate it due to php notice!
        //$initialTime = $timestamp;

        // by default we round to the full minutes
        $roundTo = 1;
        foreach ($parents as $aParent) {
            if ($aParent['roundTo']) {
                $roundTo = $aParent['roundTo'];
                break;
            }
        }
        $durationMin = $duration / 60;
        $div = floor($durationMin / $roundTo);
        // if the result is different to the actual value, then we did really round it, and 
        // since we always round up, we have to add 1, so i.e. 53 min become 60min, but 
        // 45min should stay, they should not become 60min, thats why no +1 (all for 15min rounding)
        $ret = ($div*$roundTo==$durationMin) ? $div * $roundTo : ($div + 1) * $roundTo;
        return $ret * 60;
    }

    /**
     *   updates the duration(s) of the entry that is changed
     *   1. if an entry is added, then we have to update the duration of its
     *      previous entry and if there is already a one after the new one, the new one too
     *   2. if an entry is changed, we need to update those that were before
     *      the old one and we have to do the same as for case 1
     *   3. remove like case 2, only without calling case 1 afterwards
     *
     * @param integer the time.id
     */
    function _updateDuration($data)
    {
        global $applError;

        // we dont need the joined data here, so we dont need to call preset
        $this->reset();
        if (is_numeric($data)) {
            $data = $this->get($data);
        }
        if (!($prev = $this->_getPreviousInTime( $data['timestamp'], $data['user_id'] ))) {
        	if ($next = $this->_getNextInTime( $data['timestamp'], $data['user_id'] )) {
                    $data['durationSec'] = $this->roundTime(
                        $next['timestamp'] - $data['timestamp'], $next['projectTree_id']
                    );
                    if (!$this->update($data)) {
                        $applError->log('failed updating entry with id=' . $data['id']);
                    }
        	}
            //$applError->log('couldnt get previous entry for entry with id=' . $data['id']);
        } else {
            // get the preceeding 3 elements
            $this->reset();
            $this->setWhere('timestamp>=' . $prev['timestamp'].' AND user_id=' . $data['user_id']);
            $this->setOrder('timestamp');
            $this->setSelect('id,timestamp,projectTree_id');
            $rows = $this->getAll(0, 3);

            for ($i=1; $i < sizeof($rows); $i++) {
                $updateData = array();
                $updateData['id'] = $rows[$i-1]['id'];
                // round the time before updating, this depends on the project it belongs to
                $updateData['durationSec'] = $this->roundTime(
                    $rows[$i]['timestamp'] - $rows[$i-1]['timestamp'], $rows[$i-1]['projectTree_id']
                );
                if (!$this->update($updateData)) {
                    $applError->log('failed updating entry with id=' . $rows[$i]['id']);
                }
            }
        }
        $this->setOrder();
        $this->setWhere();
    }

    /**
     *   get the element before the one with the given timestamp
     *   for the given user
     * 
     *   @param  int     the time for which to get the previous element
     *   @param  int     the user_id
     *   @return mixed   either the row or false
     */
    function _getPreviousInTime($timestamp, $uid)
    {
        $this->reset();
        $this->setWhere("timestamp < $timestamp AND user_id=$uid");
        $this->setOrder('timestamp', true);
        $prev = $this->getAll(0, 1);
        if (sizeof($prev)) {
            return $prev[0];
        }
        return false;
    }

    /**
     *   get the element after the one with the given timestamp
     *   for the given user
     *
     *   @param  int     the time for which to get the next element
     *   @param  int     the user_id
     *   @return mixed   either the row or false
     */
    function _getNextInTime($timestamp, $uid)
    {
        $this->reset();
        $this->setWhere("timestamp > $timestamp AND user_id=$uid");
        $this->setOrder('timestamp');
        $next = $this->getAll(0, 1);
        if (sizeof($next)) {
            return $next[0];
        }
        return false;
    }

    /**
     * We get the latest timestamp of given project
     * @param int $projectId
     */
    function getLastTimestampProject($projectId)
    {
        if (empty($projectId)) {
            return false;
        }

        $this->reset();
        $this->setWhere('projectTree_id=' . $projectId);
        $this->setOrder('timestamp', true);
        $this->setSelect('timestamp');
        $rows = $this->getAll(0, 1);

    	return $rows[0]['timestamp'];
    }

    /**
     * We get the duration of 5 latest timestamps of given project
     * Additionally we return the timestamp and the task for our
     * xajax check function about overbooking
     * @param int $projectId
     */
    function getLastDurationsProject($projectId)
    {
        if (empty($projectId)) {
            return false;
        }

        $this->reset();
        $this->setWhere('projectTree_id=' . $projectId);
        $this->setOrder('timestamp', true);
        $this->setSelect('timestamp,task_id,durationSec');
        $rows = $this->getAll(0, 5);

    	return $rows;
    }

    /**
     * get the element before the one with the given timestamp
     */
    function _getPreviousTimestampProject($timestamp, $projectId)
    {
        $this->reset();
        $this->setWhere("timestamp < $timestamp AND projectTree_id=$projectId");
        $this->setOrder('timestamp', true);
        $prev = $this->getAll(0, 1);
        if (sizeof($prev)) {
            return $prev[0];
        }
        return false;
    }

    /**
     * get the element after the one with the given timestamp
     */
    function _getNextTimestampProject($timestamp, $projectId)
    {
        $this->reset();
        $this->setWhere("timestamp > $timestamp AND projectTree_id=$projectId");
        $this->setOrder('timestamp', false);
        $prev = $this->getAll(0, 1);
        if (sizeof($prev)) {
            return $prev[0];
        }
        return false;
    }

    /**
     *   overwrite this method to correct the times around it
     * 
     *   @param  int     the id of the element to be removed
     */
    function remove($id)
    {
        global $applError, $userAuth, $user;

        $this->reset();
        // get the old data if there are any, before removing
        if ($oldData = $this->get($id)) {
            // if this entry is not the user's own entry, check if he is admin
            // if not he is not allowed to remove this entry
            if ($oldData['user_id'] != $userAuth->getData('id') && !$user->isAdmin()) {
                $applError->set('You don\'t have the permission to remove this entry!');
                return false;
            }
            // check if the project is available, if not dont allow removing any entries
//print 'remove: $projectTree->isAvailable( '.$oldData['projectTree_id'].' , '.$oldData['timestamp'].' )<br>';
            $projectTree = modules_project_tree::getInstance();
            if ($projectTree->isAvailable($oldData['projectTree_id'], $oldData['timestamp'])) {
                $ret = parent::remove($id);
                if ($ret) {
                    $this->_updateDuration($oldData);
                }
                return $ret;
            } else {
                $applError->set('The project is not available anymore, you must not remove the entry!');
                return false;
            }
        }
        // the element doesnt exist anymore, so it cant be removed again
        return true;
    }

    /**
     *   get the times for a period
     * 
     *   @author     Wolfram Kriesing <wk@visionp.de>
     *   @param      int     timestamp where to start, only the date is used
     *   @param      int     timestamp until when, only the date is used
     *   @return     array   the prepared result
     */
    function getDay($start = 0, $endtime = 0)
    {
        if (!$start) {
            $start = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        }
        if (!$endtime) {
            // getFiltered adds one day, to have the end of the day!
            $endtime = $start;
        }

        $data['timestamp_start'] = $start;
        $data['timestamp_end'] = $endtime;
        // sort by user_id first, so we have each user grouped together
        // sort descending, so we have the newest on top
        $data['order'] = array('user_id,timestamp', true);
        $res = $this->prepareResult($this->getFiltered($data, false));

        $this->preset();

        return $res;

/*        $res = array();

        if ($start) {
            $start = mktime(
                0, 0, 0, date('m', $start), date('d', $start), date('Y', $start)
            );
        } else {
            $start = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        }

        if ($endtime) {
            $endtime = mktime(
                0, 0, 0, date('m', $endtime), date('d', $endtime), date('Y', $endtime)
            ) + 60 * 60 * 24;
        } else {
            $endtime = $start + 60 * 60 * 24;
        }

        $this->addWhere("timestamp > $start AND timestamp < $endtime");
        // sort by user_id first, so we have each user grouped together
        // sort descending, so we have the newest on top
        $this->setOrder('user_id,timestamp',true);
        $res = $this->prepareResult($this->getAll());

        $this->preset();

        return $res;
*/
    }

    /**
     *   get time data for the given filter
     *   the $filter is any array, which can define various filter parameters
     *   which will be applied using setWhere and alike methods
     *   the following indexes are currently available:
     *       comment
     *       projectTree_ids
     *       task_ids
     *       user_ids
     *       timestamp_start
     *       timestamp_end
     *       order       -   i.e. 'name,user_id' or
     *                       to sort in DESCending array('timestamp',true)
     *       limit       -   i.e. array(0,10)
     */
    function getFiltered($filter, $preset = true)
    {
        if ($preset) {
            $this->preset();
        }

        if (@$filter['comment']) {
            $this->addWhereSearch(TABLE_TIME . '.comment', $filter['comment']);
        }
        if (@$filter['projectTree_ids'] && sizeof($filter['projectTree_ids'])) {
            $this->addWhere(TABLE_PROJECTTREE . '.id IN (' . implode(',', $filter['projectTree_ids']) . ')');
        } else {
            // if no projectTree_ids are given limit it to only the projects where
            // the user is manager in, or his own data where he is no manager!
//!!!!
        }
        if (@sizeof($filter['task_ids'])) {
            $this->addWhere(TABLE_TASK . '.id IN (' . implode(',', $filter['task_ids']) . ')');
        }
        if (@$filter['user_ids']) {
            $this->addWhere(TABLE_USER . '.id IN (' . implode(',', $filter['user_ids']) . ')');
        }
        if (@$filter['timestamp_start']) {
            $start = $filter['timestamp_start'];
            $start = mktime(0, 0, 0, date('m', $start), date('d', $start), date('Y', $start));
            $this->addWhere('timestamp>'.$start);
        }
        if (@$filter['timestamp_end']) {
            $endtime = $filter['timestamp_end'];
            $endtime = mktime(0, 0, 0, date('m', $endtime), date('d', $endtime), date('Y', $endtime)) + 60 * 60 * 24;
            $this->addWhere('timestamp < ' . $endtime);
        }
        if (@$filter['order']) {
            settype($filter['order'], 'array');
            $this->setOrder($filter['order'][0], isset($filter['order'][1]) ? $filter['order'][1] : false);
            // SX (AK) : in case we have bookings within one minute, we have to order by id as well
            // shouldn't do any harm if not ;-)
            $this->addOrder('id', true);
        }
        if (@$filter['limit']) {
            return $this->getAll($filter['limit'][0], $filter['limit'][1]);
        }
        return $this->getAll();
    }

    /**
     *   add the 'duration' to the given data sets
     *   this is the duration in hours!
     *
     *   @author     Wolfram Kriesing <wk@visionp.de>
     *   @param      array   an array of results, such as from getAll
     *   @return     array   as the input, with additional data for each result
     */
    function prepareResult($data)
    {
        $lastEntry = null;
        if (is_array($data)) {
            foreach ($data as $key => $x) {
                // we need to pass the element itself,
                // since the data get modified in _saveDuration
                $this->_saveDuration($data[$key]);
            }
        }

        return $data;
    }

    /**
     *   calculate the duration and save it in the beginTime
     *   therefore $beginTime needs to be passed as a reference
     * 
     *   @param  array   data of a time-row
     */
    function _saveDuration(&$beginTime)
    {
        if ($beginTime['_task_calcTime']) {
            $beginTime['duration'] = $this->_calcDuration($beginTime['durationSec']);
        }
    }

    /**
     *   calculate the duration
     * 
     *   @param  int     the seconds
     *   @param  string  for the following modes the result looks as described:
     *                   'hour'      => 10:00
     *                   'decimal'   => 10,5
     *                   'days'      => 1,2      a day counts 8 hours!
     *   @return string  see above
     */
    function _calcDuration($seconds, $mode = 'hour')
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds - $hours * 3600) / 60);

        switch ($mode) {
            case 'hour':
                return sprintf('%02d:%02d', $hours, $minutes);
            case 'decimal':
                // divide minutes by 60 to get the 100th parts of the hour
                // times 100 to remove the '0,'
                // jv: round() is needed for correct output in oo-templates
                $decimalMinutes = round($minutes / (60/100));
                return sprintf('%02d,%02d', $hours, $decimalMinutes);
            case 'days':
                // the days as a dec number
                $time = sprintf('%02d.%02d', $hours, $minutes / (60/100));
                $daydec = round($time/8, 2);
                // 2.3.2.2 : now we return a string with decimal places
                return str_replace('.', ',', sprintf('%01.2f', $daydec));
        }
    }

    /**
     *
     *   @author     Wolfram Kriesing <wk@visionp.de>
     *   @param
     *   @param      int     give the number of pixels a minute shall be long
     *   @return
     */
    function getImgWidth($aTime, $zoom = 10)
    {
        if (is_array($aTime)) {
            $durationSec = $aTime['durationSec'];
        } else {
            $durationSec = $aTime;
        }

        $imgWidth = ceil($durationSec / 60 / $zoom);
        if ($imgWidth < 2) {
            $imgWidth = 2;
        }
        return $imgWidth;
    }

    function getDurationImg($aTime)
    {
        $width = $this->getImgWidth($aTime);
        $tag = '<img src="' . $config->vImgRoot . '/pixel.gif'
             . '" width="' . $width . '" alt="">';
        return $tag;
    }

    /**
     *   preset the values
     *
     *   @author     Wolfram Kriesing <wk@visionp.de>
     *   @return     array   the data for an empty element
     */
    function getEmptyElement()
    {
        global $userAuth;

        $data['user_id'] = $userAuth->getData('id');
        $data['timestamp'] = time();

        return $data;
    }

} // end of class

$time = new modules_time;
