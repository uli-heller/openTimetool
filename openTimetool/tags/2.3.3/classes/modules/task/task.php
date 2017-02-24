<?php
//
//  $Log: task.php,v $
//  Revision 1.5  2002/11/29 16:53:01  wk
//  - clean up a bit
//
//  Revision 1.4  2002/10/21 18:25:56  wk
//  - added preset method
//
//  Revision 1.3  2002/09/23 09:34:01  wk
//  - added method getNoneProjectTask
//
//  Revision 1.2  2002/08/19 20:31:13  wk
//  - now tasks can also be removed if they are not assigned yet
//
//  Revision 1.1.1.1  2002/07/22 09:37:37  wk
//
//
//
require_once($config->classPath.'/modules/time/time.php');
require_once($config->classPath.'/modules/common.php');
/**
*
*
*   @package    modules
*   @version    2002/07/18
*   @access     public
*   @author     Wolfram Kriesing <wolfram@kriesing.de>
*/
class modules_task extends modules_common
{

    var $table = TABLE_TASK;

    function modules_task()
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
        $this->setOrder('name');
    }

    function getEmptyElement()
    {                
        $data['calcTime']       = 1;
        $data['needsProject']   = 1;
        return $data;
    }

    /**
    *   check if the task that shall be removed is in use
    *   if it is then we dont allow removing
    *
    */
    function remove( $id )
    {
        global $applError;

        $time = new modules_time;
        $time->setWhere( 'task_id='.$id );
        if( ($cnt=$time->getCount())>0 )
        {
            $applError->set('Sorry, this task has already been used '.$cnt.' times, it cant be removed!');
            return false;
        }
        return parent::remove($id);
    } 
                        
    /**
    *   this gets the tasks that can be logged without specifiying a project
    */
    function getNoneProjectTasks()
    {
        $this->reset();
        $this->setWhere('needsProject=0');
        $res = $this->getAll();
        // we do the reset here, to be clear in every call afterwards, since this method gets called
        // mostly before using the class somewhere else
        $this->reset();
        return $res;
    }
    
    /**
    *   this gets the tasks that have a duration and need a project
    */
    function getProjectTasks()
    {
        $this->reset();
        $this->setWhere('needsProject=1,calcTime=1');
        $res = $this->getAll();
        // we do the reset here, to be clear in every call afterwards, since this method gets called
        // mostly before using the class somewhere else
        $this->reset();
        return $res;
    }    
    
    
	/**
	 * SX (AK): 
 	 * tells if given task is a non project task 
 	*/
    function isNoneProjectTask($taskid)
    {
    	$noneProjectTasks = $this->getNoneProjectTasks();
    	$isone = false;
    	foreach($noneProjectTasks as $task)
    	{
    		if($task['id'] == $taskid) {
    			$isone = true;
    			break;
    		}
    	}
    	return $isone;
    }
	/**
	 * SX (AK): 
 	 * tells if given task is a project task 
 	*/
    function isProjectTask($taskid)
    {
    	$ProjectTasks = $this->getProjectTasks();
    	$isone = false;
    	foreach($ProjectTasks as $task)
    	{
    		if($task['id'] == $taskid) {
    			$isone = true;
    			break;
    		}
    	}
    	return $isone;
    }
    
        /**
    *   this gets the tasks that have a duration and need a project
    */
    function hasDuration($taskid)
    {
        $this->reset();
        $this->setWhere('calcTime=1');
        $res = $this->getAll();
        // we do the reset here, to be clear in every call afterwards, since this method gets called
        // mostly before using the class somewhere else
        $this->reset();

    	$isone = false;
    	foreach($res as $task)
    	{
    		if($task['id'] == $taskid) {
    			$isone = true;
    			break;
    		}
    	}
		return $isone;
    }    
    
    
}   // end of class

$task = new modules_task;
?>
