<?php

/**
 * $Id
 * 
*   use like this
*   if( $pageHandler->save($data) ) 
*   {
*       do checks here
*   }
*   // if needed
*   $data = $pageHandler->getData();
*/
class vp_Application_SaveHandler extends HTML_Template_Xipe_Options
{

    var $options = array(   'saveButton'        =>  'action_save',
                            'saveAsNewButton'   =>  'action_saveAsNew',  //
                            'primaryCol'        =>  'id'    //
                            );

    /**
    *   boolean     saves the state of the saveHandler method,
    *               if it failed it is false, get its value by using succeeded()-method
    */
    var $succeeded = false;

    /**
    *
    *
    *   @version    2002/04/02
    *   @access     public
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    */
    function vp_Application_SaveHandler( &$error , &$message , $options=array() )
    {
        $this->_error = &$error;
        $this->_message = &$message;
        $this->HTML_Template_Xipe_Options($options);
    }
                           
    /**
    *   remove the saveHandler method and use this way:
    *   if( $pageHandler->save($data) )
    *       do checks here
    *   // if needed
    *   $data = $pageHandler->getData();
    */
    function save( $data )
    {
        $this->data = $this->saveHandler($data);
        return $this->succeeded;
    }

    function getData()
    {
        return $this->data;
    }


    /**
    *   call this method to do all the handling on an edit page
    *
    *   @deprecated
    *   @version    2002/04/16
    *   @access     public
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    */
    function saveHandler( $data )
    {
        //$_REQUEST['newData'] = $this->beforeSave( $_REQUEST['newData'] );

        $getDataFromId = null;
        // shall the data be saved as new data?
        // remove the 'id' if the current data set shall be saved as a new one
        if( isset($_REQUEST[$this->getOption('saveAsNewButton')]) && $_REQUEST[$this->getOption('saveAsNewButton')])
        {
            unset($data[$this->getOption('primaryCol')]);
            $_REQUEST[$this->getOption('saveButton')] = true;
        }
        // shall data be saved?
        if( isset($_REQUEST[$this->getOption('saveButton')]) && $_REQUEST[$this->getOption('saveButton')] )
        {

            // pass the data by reference so if the save method changes the data
            // like it converts a date from 1.1.2001 to the timestamp, we are also returning that
            // this means the save routine has to be carefully written and this has to be known!!!
            if( $saved = $this->_object->save( $data ) )
            {       
                $this->succeeded = true;
                $this->_message->set('Data successfully saved.');
                if( $saved!==true ) // 'true' is only returned if the data were updated, after calling 'add'
                {
                    $getDataFromId = $saved;
                    //$this->afterAdd( $saved );
                }
                else
                    $getDataFromId = $data['id'];
            }
            else    // if saving returned with false, return the given data and tell that saving failed
            {
                $this->succeeded = false;
                //$this->_error->set('Error - saving failed.');
                return $data;
            }
        }

        // was the id passed as a get parameter?
        if( @$_REQUEST[$this->getOption('primaryCol')] )
            $getDataFromId = $_REQUEST[$this->getOption('primaryCol')];

        if($getDataFromId)
        {
            $curData = $this->_object->get($getDataFromId);
        }
        else
        {
            return $this->_object->getEmptyElement();
        }

        return $curData;
    }

    /**
    *   return the error state of the last method executed
    *   @return boolean false if any error occured
    */
    function succeeded()
    {
        return $this->succeeded;
    }


    function setObject(&$object)
    {
        $this->_object = &$object;
    }

}
?>
