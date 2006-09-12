<?php
//
//  $Log: util.php,v $
//  Revision 1.9  2003/03/04 19:10:58  wk
//  - remove ancient method, which is replaced by I18N
//  - CS
//
//  Revision 1.8  2002/12/02 10:48:42  wk
//  - simplify translate
//
//  Revision 1.7  2002/11/19 19:55:15  wk
//  - added translate method
//
//  Revision 1.6  2002/10/22 14:21:29  wk
//  - return timestamp if it is already one
//
//  Revision 1.5  2002/08/29 13:27:36  wk
//  - made makeTimestamp also convert times, like : 10:00
//
//  Revision 1.4  2002/08/22 12:41:09  wk
//  - added formatPrice
//
//  Revision 1.3  2002/08/20 16:22:52  wk
//  - extended makeTimestamp to convert the date given from the input field
//
//  Revision 1.2  2002/08/19 20:32:11  wk
//  - timestamp conversion methods
//
//  Revision 1.1.1.1  2002/07/22 09:37:37  wk
//
//
//

require_once('Date/Calc.php');

/**
*
*
*   @package    ignaz
*   @version    2002/03/28
*   @access     public
*   @author     Wolfram Kriesing <wolfram@kriesing.de>
*/
class util
{

    function translateAndPrint( $string )
    {
        global $translator, $lang;
        $translated = $translator->simpleTranslate($string,$lang);  // only translate exact matches
        echo $translated;
    }

    function translate( $string )
    {
        global $translator, $lang;
        
        // if the simple-translation didnt succeed we need to use the regExp-translation
        // and translate handles all that itself, so no extra code around it needed
        $ret = $translator->translate($string,$lang);

        return $ret;
    }

    /**
    *   translate the DAY, MONTH, YEAR values into a timestamp
    *   or if the string contains a ':' then we assume it's a time, like 10:00
    *
    *   @version    2002/03/11
    *   @access     public
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      array   the values returned by the select boxes
    *                       this is passed by reference so we can change the value directly in this method
    *   @return     mixed   true on success, or false if the date is not valid
    */
    function makeTimestamp( $dateOrTime )
    {
/* actually we dont have any of those fields
        if( is_array($date) )
        {
            if( !Date_Calc::isValidDate($date['day'], $date['month'], $date['year']) )
            {
                return false;
            }

            $date = mktime (0,0,0,$date['month'],$date['day'],$date['year']);
            return $date;
        }
        else
*/                                     
        $ret = $dateOrTime;

        $date = explode('.',$dateOrTime);
        if (sizeof($date)>1) {
            if (!Date_Calc::isValidDate($date[0],$date[1],$date[2])) {
                return false;
            }
            $ret = mktime(0,0,0,$date[1],$date[0],$date[2]);
        }

        $time = explode(':',$dateOrTime);
        if (sizeof($time)==2) {
            $ret = mktime($time[0],$time[1],1,1,1970,0);
        }
        return $ret;
    }


    function formatPrice( $price )
    {
        return sprintf('%.2f',$price);
    }

}   // end of class

$util = new util;

?>
