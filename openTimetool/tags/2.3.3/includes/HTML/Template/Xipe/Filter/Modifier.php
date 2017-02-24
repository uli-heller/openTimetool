<?php
//
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997, 1998, 1999, 2000, 2001, 2002, 2003 The PHP Group |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Wolfram Kriesing <wolfram@kriesing.de>                      |
// +----------------------------------------------------------------------+
//  $Id$
//

require_once 'HTML/Template/Xipe/Options.php';

/**
*
*
*   @package    HTML_Template_Xipe
*   @access     public
*   @version    02/06/26
*   @author     Wolfram Kriesing <wolfram@kriesing.de>
*/
class HTML_Template_Xipe_Filter_Modifier extends HTML_Template_Xipe_Options
{

    var $_imgDirs = array();
    var $_imgFiles = array();

    /**
    *   this filter trys to read all the following tags and replaces the src tags
    *   with the complete file name (w/o the http://domain)
    *       <img src> <input src>
    *   Using this filter makes it easier to work without looking up
    *   where the image really is located every time
    *   You simply need to give the image name and this filter searches for
    *   the image in the image root and rewrites the image name including
    *   the complete path to the image, so this saves time when developing and
    *   no php processing is necessary anymore when you have image tags like this:
    *   &lt;img src="{$imgRoot}/dir/name/image"&gt;<br>
    *   <p>
    *   Why not make the resulting link relative to the current URL (PHP_SELF)?<br>
    *   Because a compiled template might be included from multiple places, so the
    *   relative path would not always be the same. Thats why we make it absolute
    *   w/o the protocol and domain in front.
    *   </p>
    *
    *   @version    02/06/27
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  the original template code
    *   @param      string  the virtual image path
    *   @param      string  here you can set the prefered image type
    *                       so that you only need to give the image name w/o its extension
    *                       and if multiple images are found the prefered one is used
    *   @return     string  the modified template
    */
    function imgSrc( $input , $imageRoot , $vImgRoot , $preferedType='gif' , $dropDirs=array('CVS'))
    {
        $_imgTypes = array('gif','jpg','png');
                                        
        settype($imageRoot,'array');
        settype($vImgRoot,'array');

// FIXXME make img src tags relative if desired
        // put the prefered type first, so we find it first :-)
        $imgTypes = array($preferedType);
        foreach( $_imgTypes as $aImgType )
            if( $aImgType != $preferedType )
                $imgTypes[] = $aImgType;

        $found = array();

        // modify the vImgRoot NOT to contain the 'http://domain' string in front
        // since this is only unnecessary text
        foreach( $vImgRoot as $key=>$aDir )
            $vImgRoot[$key] = preg_replace('/^http.?:\/\/[^\/]+/','',$vImgRoot[$key]);

        $regExp = '/<[img|input].+src="(.*)"/Ui';
        preg_match_all($regExp,$input,$images);
        if(sizeof($images[1]))
        {
            if( !sizeof($this->_imgDirs) )  // get image dirs if we didnt yet, since this instance might be used multiple times
            {
                foreach( $imageRoot as $aDir )
                {
                    $this->_getDirs($aDir,$dropDirs);
                    $this->_imgDirs = array_merge($this->_imgDirs,$this->_foundDirs);
                }
            }

            // go thru all the images we have found and find their path
            foreach( $images[1] as $aImage)
            {
                if( isset($this->_imgFiles[$aImage]) && $this->_imgFiles[$aImage] )
                    $found[$aImage] = $this->_imgFiles[$aImage];
                else
                {
                    if( sizeof($this->_imgDirs) )
                    foreach( $this->_imgDirs as $aDir ) // go thru all the directories found
                    {
                        // using pathinfo returns also the file's extension
                        $fileInfo = pathinfo($aImage);
                        // if there is an extension we assume that this one is used
                        $_imgTypes = isset($fileInfo['extension']) && $fileInfo['extension'] ? array('') : $imgTypes;
                        foreach( $_imgTypes as $aType ) // if no file extension given loop through all possible imgTypes
                        {
                            $aType = $aType ? ".$aType" : '';
//print "....check $aDir $aImage$aType<br>";
                            if( @file_exists($aDir.$aImage.$aType))
                            {
                                foreach( $imageRoot as $key=>$aImgDir )
                                {
                                    if( strpos( realpath($aDir.$aImage.$aType),$aImgDir ) === 0 )
                                        $this->_imgFiles[$aImage] = str_replace($aImgDir,$vImgRoot[$key],realpath($aDir.$aImage.$aType));
                                }
//print 'found use:'.$this->_imgFiles[$aImage].'<br>';
                                break(2);
                            }
                            if( @file_exists($aDir.'/'.$aImage.$aType))
                            {
                                foreach( $imageRoot as $key=>$aImgDir )
                                {
                                    if( strpos( realpath($aDir.'/'.$aImage.$aType),$aImgDir ) === 0 )
                                        $this->_imgFiles[$aImage] = str_replace($aImgDir,$vImgRoot[$key],realpath($aDir.'/'.$aImage.$aType));
                                }
//print 'found use:'.$this->_imgFiles[$aImage].'<br>';
                                break(2);
                            }
                        }
                    }
                }
            }
        }

        if( sizeof($this->_imgFiles) )
        foreach( $this->_imgFiles as $file=>$vName )
        {
            $_file = str_replace('/','\\/',preg_quote($file));  //"
            $regExp = '/<(img|input)(.+)src="'.$_file.'"/Ui';
            $input = preg_replace($regExp,'<$1$2src="'.$vName.'"',$input);
        }

        return $input;
    }

    /**
    *   find all dirs in the given one, writes them into _foundDirs
    *   be sure to copy them from there when using
    *   this method calls itself recursively to get all subdirs too
    *   watch out if there are links which could cause endless loops
    *   i havent checked if that can happen
    *
    *   @version    02/06/27
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  the directory under which to search
    *   @param      array   directories that shall be left out, like CVS
    */
    function _getDirs( $root , $dropDirs=array() )
    {
        $dirs = array();
        if ($handle = @opendir($root))
        {
            $dirs[] = '';   // do also include the directory itself
            while (false !== ($file = readdir($handle)))
            {
                if( $file!='.' &&  $file!='..' && is_dir($root.'/'.$file) && !in_array($file,$dropDirs))
                {
                    $dirs[] = $file;
                }
            }
            closedir($handle);
        }

        sort($dirs);
        foreach( $dirs as $aDir )
        {
            $this->_foundDirs[] = $root.'/'.$aDir;
            if( $aDir )
                $this->_getDirs($root.'/'.$aDir,$dropDirs);
        }
    }


    /**
    *   correct all links, if they are not proper like www.home.de
    *   then they will be corrected to be http://www.home.de
    *   a link checker could be implemented too :-/
    *
    *   @version    02/06/27
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  the original template code
    *   @param      string  the virtual image path
    *   @return     string  the modified template
    */
    function aHref( $input )
    {
    }

    /**
    *   replace text with links
    *
    *   @version    02/08/02
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  the original template code
    *   @param      array   an array of text that shall be replaces by the links
    *                       'text'  =>  'http://www.url.de'
    *   @return     string  the modified template
    */
    function autoLink( $input , $links )
    {
//FIXXME replace ONLY text, not stuff like '<a href="....index.php">' dont replace the php there too !!!!
// stuff like <a href>pear.php.net</a> would result in <a href><ahref>pear</a>.<ahref>php</a>.net</a>
// if pear and php were to be linked automatically ... prevent from doing that !!!
        if( sizeof($links) )
        foreach( $links as $word=>$link )
        {
            $input = preg_replace(  '/(>[^<]*)('.$word.')([^>]*<)/Ui' ,
                                    '$1<a href="'.$link.'" target="_blank">$2</a>$3' ,
                                    $input );
        }
        return $input;
    }
}
?>
