<?php
//
//  $Log: HTML.php,v $
//  Revision 1.2  2003/03/11 12:57:56  wk
//  *** empty log message ***
//
//  Revision 1.2  2003/03/04 16:11:57  pp
//  - added alt="" to img html-tag
//
//  Revision 1.1  2002/06/20 16:57:55  wk
//  - new method getSpacer
//
//

/**
*   this class contains commonly used methods for HTML
*   
*
*   @package    vp_Util
*   @version    2002/03/28
*   @access     public
*   @author     Wolfram Kriesing <wolfram@kriesing.de>
*   @requires   $config->imgRoot to be set
*               pixel.gif inside $config->imgRoot to be a transparent 1x1 image
*/
class vp_Util_HTML
{

    /**
    *   returns an transparent image-tag of the given size
    *
    *   @version    2002/04/29
    *   @access     public
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      int     the width of the space
    *   @param      int     the height of the space
    *   @return     string  the img-tag
    */
    function getSpacer($width,$height=1)
    {
        global $config;

        $string = sprintf(  '<img src="%s" width="%s" height="%s" alt="">',
                            $config->vImgRoot.'/pixel.gif',
                            $width,$height);
        return $string;
    }

}   // end of class

