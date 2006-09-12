<?php
//
//  $Log: Message.php,v $
//  Revision 1.2  2003/03/11 12:57:56  wk
//  *** empty log message ***
//
//  Revision 1.9  2003/01/27 18:13:56  wk
//  - ooops still using simpletemplate
//
//  Revision 1.8  2002/11/28 10:28:48  wk
//  - made it possible to add the db-connection later, since it might not be setup at the time when an instance of this object is needed
//
//  Revision 1.7  2002/11/26 12:58:32  wk
//  - bugfix in getAll
//
//  Revision 1.6  2002/11/19 19:54:38  wk
//  - return arrays too
//
//  Revision 1.5  2002/10/04 11:44:08  wk
//  - added existAnyText method
//
//  Revision 1.4  2002/08/29 13:39:27  wk
//  - added setOnce and logOnce
//
//  Revision 1.3  2002/07/22 13:25:36  wk
//  - bug fix in getAllText, get really only the text-fields
//
//  Revision 1.2  2002/07/08 09:50:34  wk
//  - handle properly if the constructor is called without parameters
//
//  Revision 1.1  2002/06/19 15:24:58  wk
//  - first checkin for vp
//
//

require_once('vp/OptionsDB.php');

/**
*   Description
*
*   @package  vp_Page
*   @access   public
*   @version  01/12/10
*   @author   Wolfram Kriesing <wolfram@kriesing.de>
*/
class vp_Page_Message extends vp_OptionsDB
{
// FIXXMEE i think this class should use the PEAR and PEAR_Error whic already
// implements a very powerful error handling
// the only thing it doesnt do it collecting errors, like we do here in a 'list'
// have a look one day
    /**
    *   collects all the messages
    *
    *   @var    array   $list
    */
    var $list = array();

    function vp_Page_Message( $dbDSN=null , $options=array() )
    {
        if( $dbDSN==null )
            $this->HTML_Template_Xipe_Options($options);
        else
            parent::vp_OptionsDB( $dbDSN , $options );
    }

    function setDbConnection( $dbDSN )
    {
        parent::vp_OptionsDB( $dbDSN , $this->options );
        $this->flushData();
    }                      
                                   
    /**
    *   this is called to flush all the current messages
    *   it flushes all the message-list into the persistent storage
    *   overwrite it if needed, we dont save messages by default, but error needs it!
    *
    */
    function flushData()
    {
    }

    /**
    *   set a message, will be added to the message list
    *
    *   @access     public
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      $error  array   an error that can contain a lot of error info in an associatve array,
    *                               or simply the message, this might not be an array
    *                               'text'  => contains the standard message that can also be shown to the user
    *                               'log'   => the message that is supposed to be for the developer, should go in a log file
    */
    function set($message)
    {
        if( is_string($message) )
            $this->list[]['text'] = $message;
        else
            $this->list[] = $message;
    } // end of function
    
    /**
    *   set the given message only once in the list
    *   @param  string  the message
    */
    function setOnce($message)
    {
        if( !$this->hasMessage($message,'text') )
            $this->set($message);
    }

    /**
    *   set a message, which shall only be logged
    *
    *   @access     public
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  $message    log message
    */
    function log($message)
    {
        $this->list[]['log'] = $message;
    }
    function logOnce($message)
    {
        if( !$this->hasMessage($message,'log') )
            $this->log($message);
    }

    /**
    *   returns true if any message occured
    *
    *   @access     public
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    */
    function existAny()
    {
        if(sizeof($this->list))
            return true;
        return false;
    } // end of function

    /**
    *   returns true if any message occured
    *
    *   @access     public
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    */
    function existAnyText()
    {                          
        if(sizeof($this->list))
        {
            foreach( $this->list as $aMsg)
                if( $aMsg['text'] )
                    return true;
        }
        return false;
    } // end of function

    /**
    *   returns the entire message list
    *
    *   @access     public
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    */
/*    function getAll()
    {
        return $this->list;
    } // end of function
*/

    /**
    *   returns the entire error list
    *
    *   @access     public
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  seperate the text's by the given string
    *                       if the string is null an array is returned
    *   @return     mixed   either a string with all the messages sepearated by the seperator
    *                       or an array of all the messages
    */
    function getAllText( $seperator='<br>' )
    {
        if( $seperator == null )
            $text = array();
        else
            $text = '';

        foreach( $this->list as $aMsg)
            if( $aMsg['text'] )
                if( $seperator == null )
                    $text[] = $aMsg['text'];
                else
                    $text.= $aMsg['text'].$seperator;

        return $text;
    } // end of function

    /**
    *   returns the entire error list, all the logs and normal messages
    *   this is actually only for debugging not really for a live version
    *
    *   @access     public
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @return     mixed   either a string with all the messages sepearated by the seperator
    *                       or an array of all the messages
    */
    function getAll($seperator='<br>')
    {
        if( $seperator == null )
            $text = array();
        else
            $text = '';

        foreach( $this->list as $aMsg)
        {
            foreach( $aMsg as $kind=>$aString )
            {
                if( $seperator == null )
                    $text[] = strtoupper($kind).': '.$aString;
                else
                    $text.= strtoupper($kind).': '.$aString.$seperator;
            }
        }
        return $text;
    } // end of function

    /**
    *   check if the given message is already saved
    *
    */
    function hasMessage( $message , $kind='' )
    {
        foreach( $this->list as $aMsg)
        {
            foreach( $aMsg as $curKind=>$aString )
            {
                if( $aString==$message )
                {
                    if( $kind )
                        if( $kind==$curKind )
                            return true;
                    else
                        return true;
                }
            }
        }
        return false;
    }

} // end of class
?>
