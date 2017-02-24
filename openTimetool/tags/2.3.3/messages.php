<?php
    /**
     * 
     * $Id$
     * 
     * ************** switch to SVN *************
    *  $Log: messages.php,v $
    *  Revision 1.2  2002/12/02 15:26:04  wk
    *  - added some JS-text
    *
    *  Revision 1.1  2002/11/29 17:04:29  wk
    *  - initial commit
    *
    */
     
    /**                                                          
    * we can not use define here, since we want to translate those messages
    * and we do like this in a template: {$T_MSG_REMOVE_CONFIRM}
    * to translate it
    */ 

    $MSG_REMOVE_CONFIRM = 
                            'Are you sure to delete this entry?';
                             
    // this is used on the time/period-log                            
    $MSG_LOG_FOR_PROJECT =  'Log for the project';
    $MSG_FOR             =  'for:';
    $MSG_ARE_YOUR_SURE =    'Are you sure?';
    
    // Project overbooking alerts
    $MSG_PROJECT_OVERBOOKED = 'CAUTION: Project overbooked!';
    $MSG_PROJECT_OVERBOOKED21 = "CAUTION: Only ";
    $MSG_PROJECT_OVERBOOKED22 = "hours left";
	$MSG_PROJECT_BOOKING_CHOICE_QUESTION = "Do you still want to book ?";
	$MSG_PROJECT_BOOKING_CHOICE_CANCEL = "CANCEL : No booking!";
	$MSG_PROJECT_BOOKING_CHOICE_OK = "OK : Booking will be done! (Project overbooked)";
	?>
