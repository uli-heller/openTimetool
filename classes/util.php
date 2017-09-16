<?php
/**
 * 
 * $Id$
 * 
 */

require_once 'Date/Calc.php';

/**
 * 
 * 
 * @package    ott
 * @version    2002/03/28
 * @access     public
 * @author     Wolfram Kriesing <wolfram@kriesing.de>
 */
class util
{
    function translateAndPrint($string)
    {
        global $translator, $lang;

        // only translate exact matches
        $translated = $translator->simpleTranslate($string, $lang);
        //$translated = $this->convToISO($translated); // SX
        echo $translated;
    }

    function translate($string)
    {
        global $translator, $lang;

        // if the simple-translation didnt succeed we need to use the regExp-translation
        // and translate handles all that itself, so no extra code around it needed
        $ret = $translator->translate($string, $lang);
        //$ret = $this->convToISO($ret); // SX

        return $ret;
    }

    /**
     * translate the DAY, MONTH, YEAR values into a timestamp
     * or if the string contains a ':' then we assume it's a time, like 10:00
     * 
     * @version    2002/03/11
     * @access     public
     * @author     Wolfram Kriesing <wolfram@kriesing.de>
     * @param      array   the values returned by the select boxes
     *                     this is passed by reference so we can change the value directly in this method
     * @return     mixed   true on success, or false if the date is not valid
     */
    function makeTimestamp($dateOrTime)
    {
/* actually we dont have any of those fields
        if (is_array($date)) {
            if (!Date_Calc::isValidDate($date['day'], $date['month'], $date['year'])) {
                return false;
            }
            $date = mktime (0, 0, 0, $date['month'], $date['day'], $date['year']);
            return $date;
        } else
*/
        $ret = $dateOrTime;

        $date = explode('.', $dateOrTime);
        if (sizeof($date) > 1) {
            if (!Date_Calc::isValidDate($date[0], $date[1], $date[2])) {
                return false;
            }
            $ret = mktime(0, 0, 0, $date[1], $date[0], $date[2]);
        }

        $time = explode(':', $dateOrTime);
        if (sizeof($time) == 2) {
            $ret = mktime($time[0], $time[1], 1, 1, 1970, 0);
        }
        return $ret;
    }

    function formatPrice($price)
    {
        return sprintf('%.2f', $price);
    }

    // SX: conv to iso to have tstrings correctly translated to iso
    function convToISO($str)
    {
        $curenc = mb_detect_encoding($str, "UTF-8, ISO-8859-1, WINDOWS-1252 , GBK", true);
        if ($curenc != "ISO-8859-1") {
            return iconv($curenc, "ISO-8859-1", $str);
        }
        return $str;
    }

    function recRemDir($dir, $skip = '')
    {
        static $ret = array();
        foreach (glob("{$dir}/*") as $file) {
            if (is_dir($file)) {
                $this->recRemDir($file, $skip);
            } else {
                $ret[] = str_replace($_SERVER['DOCUMENT_ROOT'], '', $file)
                       . ' ... ' . (@unlink($file) ? 'ok' : 'error');
            }
        }
        if (realpath($dir) === realpath($skip)) {
            $res = 'skip';
        } else {
            $res = @rmdir($dir) ? 'ok' : 'error';
        }
        $ret[] = str_replace($_SERVER['DOCUMENT_ROOT'], '', $dir) . ' ... ' . $res;
        return $ret;
    }

} // end of class

$util = new util;
