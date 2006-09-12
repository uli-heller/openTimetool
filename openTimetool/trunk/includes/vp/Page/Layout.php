<?php
//
//  $Log: Layout.php,v $
//  Revision 1.2  2003/03/11 12:57:56  wk
//  *** empty log message ***
//
//  Revision 1.9  2003/01/27 10:05:34  wk
//  - use real-script name
//
//  Revision 1.8  2002/10/24 18:40:12  wk
//  - why make it hard when u can have it easy :-)
//
//  Revision 1.7  2002/10/24 14:18:50  wk
//  - made it possible to use css.php file in layout dirs
//
//  Revision 1.6  2002/10/04 11:43:44  wk
//  - made setLayout takes the fallback layout as a param too - not final this way!
//
//  Revision 1.5  2002/07/08 09:50:14  wk
//  - unify content1 handling
//
//  Revision 1.4  2002/07/05 17:59:29  wk
//  - get the php files also from the layout dir
//
//  Revision 1.3  2002/07/04 15:58:27  pp
//  - fix in setContent1File
//
//  Revision 1.2  2002/07/02 10:52:08  wk
//  - bug fix for content1 files which are not in the DOCUMENT_ROOT
//
//  Revision 1.1  2002/06/19 15:24:58  wk
//  - first checkin for vp
//
//

require_once('vp/OptionsDB.php');

/**
*   this class handles global layout stuff
*   dont save this object in the session !!
*
*   @package  vp_Page
*   @access   public
*   @author   Wolfram Kriesing <wolfram@kriesing.de>
*
*/
class vp_Page_Layout extends vp_OptionsDB
{

    var $options = array(
                            'layoutPath'        => '',
                            'virtualLayoutPath' => '',
                            'layout'            =>  '',
                            'fallbackLayout'    =>  false,
                            'headerFilename'    =>  'modules/header',
                            'headlineFilename'  =>  'modules/headline',
                            'navigationFilename'=>  'modules/navigation',
                            'navigation1Filename'=> 'modules/navigation1',
                            'mainFilename'      =>  'modules/main',
                            'footerFilename'    =>  'modules/footer',
                            'cssFilename'       =>  'modules/style.css',
                            'todoFilename'      =>  'todo',

                            'phpExtension'      =>  'php',  // the extension of the php files
                            'templateExtension' =>  'tpl',  // the extension of the template files

                            'defaultContentFile'=>  '',

                            'templateDir'       =>  '',  //
                            'virtualTemplateDir'=>  ''   //
                            );

    /**
    *   @var    this contains the content template if it is different from the default,
    *   @see    getContentTemplate()
    */
    var $contentTemplate = '';

    var $content1File = false;

    /**
    *
    *
    *   @access     public
    *   @version    2001/12/06
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param
    *   @return
    *
    */
    function vp_Page_Layout( $options )
    {
        foreach( $options as $key=>$value )
            $this->setOption( $key , $value );
    }   // end of function

    /**
    *   set the layout that shall be used currently
    *
    *   @access     public
    *   @version    2001/12/06
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  $layout the name of the layout
    *   @return     boolean
    */
    function setLayout( $layout=false )
    {
        if( $layout===false && $this->getOption('fallbackLayout') )
            $this->setOption( 'layout' , $this->getOption('fallbackLayout') );
                                             
//FIXXME if $layout is an array it may contain many more layout-names, so we need to build this class 
// this way, that it works with a number of so called fallback's like class-inheritance ,..
// if the file dont exist in the one layout got to the next until u have found it
        if( is_array($layout) )
        {                   
            $this->setOption( 'layout' , $layout[0] );
            $this->setOption( 'fallbackLayout' , $layout[1] );
            $layout = $layout[0];
        }

        return $this->setOption( 'layout' , $layout );
    } // end of function

    /**
    *   get the current layout
    *
    *   @access     public
    *   @version    2001/12/06
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @return     string  the current layout
    */
    function getLayout( $fallback=false )
    {
        return $fallback ? $this->getOption('fallbackLayout') : $this->getOption('layout');
    } // end of function

    /**
    *   get all available layouts
    *
    *   @access     public
    *   @version    2002/03/24
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @return     array   all the layouts that are available
    */
    function getAvailableLayouts()
    {
        $ret = array();
        $handle=opendir($this->_getLayoutRoot());
        while (false !== ($file = readdir($handle)))
        {
            if( $file!='.' && $file!='..' && $file!='CVS' && is_dir($this->_getLayoutRoot().'/'.$file) )
                $ret[] = $file;
        }
        closedir($handle);
        return $ret;
    } // end of function

    /**
    *
    *
    *   @access     public
    *   @version    2001/12/06
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @return
    */
    function _getLayoutRoot( $virtual=false )
    {
        $path = $virtual ? $this->options['virtualLayoutPath'] : $this->options['layoutPath'];
        return $path;//.'/'.$this->getLayout($fallback);
    } // end of function

    /**
    *
    *
    *   @access     public
    *   @version    2001/12/06
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      boolean $fallback   if true get the fallback layout path
    *   @return
    */
    function getLayoutPath( $virtual=false , $fallback=false)
    {
        return $this->_getLayoutRoot($virtual).'/'.$this->getLayout($fallback);
    } // end of function

    /**
    *
    *
    *   @access     public
    *   @version    2002/04/14
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      boolean $virtual    get the cirtual dir?
    *   @return
    */
    function getTemplateDir( $virtual=false )
    {
        return $virtual ? $this->getOption('virtualTemplateDir') : $this->getOption('templateDir');
    } // end of function

    /**
    *
    *
    *   @access     public
    *   @version    2001/12/06
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param
    *   @return
    */
    function setContentFile($contentFile)
    {
        $this->contentFile = $contentFile;
    } // end of function

    /**
    *
    *
    *   @access     public
    *   @version    2001/12/06
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param
    *   @return
    */
    function getContentFile( $virtual=false )
    {
        if( !$this->contentFile )
            $this->contentFile = $this->getDefaultContentFile();
        if( $virtual )
            return $this->_makeVirtual( $this->contentFile );
        return $this->contentFile;
    } // end of function

    /**
    *
    *
    *   @access     public
    *   @version    2002/03/11
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param
    *   @return
    */
    function setContent1File($file=false)
    {
        // remove parameters which might be attached to the url
        $_file = preg_replace('/\?.*/','',$file);   //"
        if( file_exists($this->getOption('templateDir').$_file) )
        {
            // set the complete path to the cotnent1 file, so getContent1File returns the
            // complete path, as all the other methods do too
            $file = $this->getOption('templateDir').$file;
        }
        $this->content1File = $file;
    } // end of function

    /**
    *
    *
    *   @access     public
    *   @version    2002/03/11
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param
    *   @return
    */
    function getContent1File( $virtual=false )
    {
        if( $virtual )
            return $this->_makeVirtual( $this->content1File );
        return $this->content1File;
    } // end of function

    /**
    *   this method returns the start page in most cases
    *
    *   @access     public
    *   @version    2001/12/06
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param
    *   @return
    */
    function getDefaultContentFile( $virtual=false )
    {
        if( $virtual )
            return $this->_makeVirtual( $this->options['defaultContentFile'] );
        return $this->options['defaultContentFile'];
    } // end of function

    /**
    *
    *
    *   @access     public
    *   @version    2001/12/06
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param
    *   @return
    */
    function getHeaderFile()
    {
//        return $this->getTemplateDir().'/'.$this->options['headerFilename'].'.'.$this->options['phpExtension'];
        return $this->getFileOrFallback( $this->options['headerFilename'].'.'.$this->options['phpExtension'] );
    } // end of function

    /**
    *
    *
    *   @access     public
    *   @version    2001/12/06
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param
    *   @return
    */
    function getHeadlineFile()
    {
        //return $this->getTemplateDir().'/'.$this->options['headlineFilename'].'.'.$this->options['phpExtension'];
        return $this->getFileOrFallback( $this->options['headlineFilename'].'.'.$this->options['phpExtension'] );
    } // end of function

    /**
    *
    *
    *   @access     public
    *   @version    2001/12/06
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param
    *   @return
    */
    function getNavigationFile()
    {
        return $this->getFileOrFallback( $this->options['navigationFilename'].'.'.$this->options['phpExtension'] );
    } // end of function

    /**
    *
    *
    *   @access     public
    *   @version    2002/03/11
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param
    *   @return
    */
    function getNavigation1File()
    {
//        return $this->getTemplateDir().'/'.$this->options['navigation1Filename'].'.'.$this->options['phpExtension'];
        return $this->getFileOrFallback( $this->options['navigation1Filename'].'.'.$this->options['phpExtension'] );
    } // end of function

    /**
    *   defines of what kind the main file is: 'dialog' or whatever
    *
    *   @access     public
    *   @version    2001/12/07
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param
    *   @return
    */
    function setMainLayout( $type )
    {
        $this->setOption( 'mainFilename' , $type );
    } // end of function

    /**
    *
    *
    *   @access     public
    *   @version    2001/12/06
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param
    *   @return
    */
    function getMainFile()
    {
        //return $this->getTemplateDir().'/'.$this->options['mainFilename'].'.'.$this->options['phpExtension'];
        return $this->getFileOrFallback( $this->options['mainFilename'].'.'.$this->options['phpExtension'] );
    } // end of function

    /**
    *   gets the css file
    *
    *   @access     public
    *   @version    2001/12/06
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      boolean     if to get the virtual file or not, default is true
    *                           since the css is mostly needed virtually
    *   @return
    */
    function getCssFile( $virtual=true )
    {
// FIXXME find out the current url (PHP_SELF) and make the style.css relative, to save code in the html file
        return $this->getFileOrFallback( $this->options['cssFilename'].'.'.$this->options['phpExtension'] , $virtual );
//        return $this->getTemplateDir(true).'/'.$this->options['cssFilename'].'.'.$this->options['phpExtension'];
    } // end of function

    /**
    *
    *
    *   @access     public
    *   @version    2001/12/06
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param
    *   @return
    */
    function getFooterFile()
    {
        //return $this->getTemplateDir().'/'.$this->options['footerFilename'].'.'.$this->options['phpExtension'];
        return $this->getFileOrFallback( $this->options['footerFilename'].'.'.$this->options['phpExtension'] );
    } // end of function

    /**
    *   returns the filename including the path that is requested
    *   if it is not found in the current layout path default is used
    *
    *   @access     public
    *   @version    2001/12/06
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  the filename to check for in the templatePath
    *   @param      boolean if this is true the path is returned as the virtual path starting with 'http://'
    *   @return     string  the file including the path
    */
    function getFileOrFallback( $filename , $virtual=false )
    {
        $fileToCheck = $this->getLayoutPath().'/'.$filename;  // get the real file, not the virtual, to check if it exists
        if( !file_exists( $fileToCheck ) )          // if the file doesnt exist return the file from the default layout
        {
            if( $this->getOption('fallbackLayout') )
            {
                $fileToCheck = $this->getLayoutPath(false,true).'/'.$filename;  // get the real file, not the virtual, to check if it exists
                if( file_exists( $fileToCheck ) )
                    return $this->getLayoutPath($virtual,true).'/'.$filename;
            }
            $fileToCheck = $this->getTemplateDir( $virtual ).'/'.$filename;
        }
        else
        {
            $fileToCheck = $this->getLayoutPath( $virtual ).'/'.$filename;// if file exists, get the (virtual) layout path for it
        }

        return $fileToCheck;
    } // end of function


    /**
    *   this returns the todo-file, which i usually update so i dont forget certain ideas
    *   or tasks that need to be done for this application
    *
    *   @access     public
    *   @version    2001/12/06
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param
    *   @return
    */
    function getTodoFile()
    {
//        return $this->options['todoFilename'];
//        return $this->getTemplateDir().'/'.$this->options['todoFilename'].'.'.$this->options['phpExtension'];
        return $this->getFileOrFallback( $this->options['todoFilename'].'.'.$this->options['phpExtension'] );
    } // end of function

    /**
    *
    *
    *   @access     public
    *   @version    2001/12/13
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param
    *   @return
    */
    function getHeaderTemplate($compiled=false)
    {
        if( $compiled )
            return $this->_compiledHeaderTemplate;

        return $this->getFileOrFallback($this->options['headerFilename'].'.'.$this->options['templateExtension']);
    } // end of function

    /**
    *
    *
    *   @access     public
    *   @version    2001/12/13
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param
    *   @return
    */
    function getHeadlineTemplate($compiled=false)
    {
        if( $compiled )
            return $this->_compiledHeadlineTemplate;

        return $this->getFileOrFallback($this->options['headlineFilename'].'.'.$this->options['templateExtension']);
    } // end of function

    /**
    *
    *
    *   @access     public
    *   @version    2001/12/13
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param
    *   @return
    */
    function getNavigationTemplate($compiled=false)
    {
        if( $compiled )
            return $this->_compiledNavigationTemplate;

        return $this->getFileOrFallback($this->options['navigationFilename'].'.'.$this->options['templateExtension']);
    } // end of function

    /**
    *
    *
    *   @access     public
    *   @version    2002/03/11
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param
    *   @return
    */
    function getNavigation1Template($compiled=false)
    {
        if( $compiled )
            return $this->_compiledNavigation1Template;

        return $this->getFileOrFallback($this->options['navigation1Filename'].'.'.$this->options['templateExtension']);
    } // end of function

    /**
    *
    *
    *   @access     public
    *   @version    2001/12/13
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param
    *   @return
    */
    function getMainTemplate($compiled=false)
    {
        if( $compiled )
            return $this->_compiledMainTemplate;

        return $this->getFileOrFallback($this->options['mainFilename'].'.'.$this->options['templateExtension']);
    } // end of function

    /**
    *
    *
    *   @access     public
    *   @version    2001/12/13
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param
    *   @return
    */
    function getFooterTemplate($compiled=false)
    {
        if( $compiled )
            return $this->_compiledFooterTemplate;

        return $this->getFileOrFallback($this->options['footerFilename'].'.'.$this->options['templateExtension']);
    } // end of function

    function getTodoTemplate($compiled=false)
    {
        if( $compiled )
            return $this->_compiledTodoTemplate;

        return $this->getFileOrFallback($this->options['todoFilename'].'.'.$this->options['templateExtension']);
    } // end of function
    /**
    *
    *
    *   @access     public
    *   @version    2001/12/16
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param
    *   @return
    */
    function getCssTemplate()
    {
// FIXXME this doesnt let us specify any style sheet anywhere (i.e. as needed for convert) its always relative to the
// layout path
        return $this->getFileOrFallback($this->options['cssFilename'].'.'.$this->options['templateExtension']);
    } // end of function

    /**
    *
    *
    *   @access     public
    *   @version    2001/12/16
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param
    *   @return
    */
    function setCssTemplate( $filename )
    {
        $this->options['cssFilename'] = $filename;
    } // end of function

    /**
    *   set the template that shall be used, only needed if it differs from the
    *   standard rules
    *   @see    getContentTemplate()
    *
    *   @access     public
    *   @version    2001/12/16
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  $file   the file, relative to the templateDir
    */
    function setContentTemplate( $file )
    {
        $this->contentTemplate = $file;
    }

    function getContent1Template($compiled=false)
    {
        if( $compiled )
            return $this->_compiledContent1Template;

        if( $this->content1File )
            return $this->getContentTemplate($this->content1File);
        return false;
    }
    /**
    *   this gets the template that shall be shown now
    *   if no $file is given PHP_SELF is used to retreive the template
    *   (gets the path and file name and adds the template extension instead of the php-extension)
    *   in any case: first the template will be searched in the current layout path
    *   if there is no template for this the template from the path where the php file is in
    *   will be used,
    *   if this doesnt exist either the defaultContentFile will be used -- i am not to sure if this is really cool
    *   it works for me now ... but for others too and in general?
    *
    *   @access     public
    *   @version    2001/12/14
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  $file   mostly this simply is __FILE__ if given at all
    *   @return
    */
    function getContentTemplate( $file='' , $compiled=false)
    {
        if( $compiled )
            return $this->_compiledContentTemplate;

        // if we are on a windows system $file may contains backslashes instead of slashes
        if( isset($_SERVER['WINDIR']) || isset($_SERVER['windir']) )
        {
            if( strpos($file,'\\')!==false )
                $file = str_replace('\\','/',$file);
        }

        if ($file=='') {
            //$file = str_replace( $this->getOption('templateDir') , '' , $_SERVER['DOCUMENT_ROOT'].$_SERVER['PHP_SELF'] );
            $file = str_replace( $this->getOption('templateDir') , '' , realpath($_SERVER['SCRIPT_FILENAME']) );
        }

        // correct the DOCUMENT_ROOT, in case it ends with a slash
        // some server setups might have that :-/
//        $docRoot = $_SERVER['DOCUMENT_ROOT'];
//        if( substr($_SERVER['DOCUMENT_ROOT'],-1) == '/' )
//            $docRoot = substr($_SERVER['DOCUMENT_ROOT'],0,-1);
        $docRoot = $this->getOption('templateDir');
        if( substr($this->getOption('templateDir'),-1) == '/' )
            $docRoot = substr($this->getOption('templateDir'),0,-1);

        if( $this->contentTemplate=='' )            // if already a template is set, dont try to retreive it yourself
        {
//print $docRoot.$file.'<br>';
            // take the url and substract the templateDir from the front and
            // strip off the file extension for the proper template to be compiled
            $reqUrl = $docRoot.$file; // 'subtract' templateDir from this string
            $pos = strpos(strrev($reqUrl),strrev( $this->getOption('templateDir') ));
            $templateName = substr(substr($reqUrl, -$pos),0,-(strlen($this->getOption('phpExtension'))+1) );

            $curTemplate = $templateName.'.'.$this->getOption('templateExtension');
//print "curTemplate = $curTemplate<br>";
            // FIXXME, index.php doesnt have a template
            //    if( !file_exists($curTemplate) )
            //    {
            //        print $curTemplate = $page->getDefaultContentFile();
            //    }

            // search for a template in the layout folder, if there is none
            // use the one which is here in this folder, the folder where the php file is (PHP_SELF)
            $layoutTemplateFile = $this->getLayoutPath().$curTemplate;
//print "layoutTemplateFile = $layoutTemplateFile<br>";
            if(file_exists($layoutTemplateFile))
                $curTemplate = $layoutTemplateFile;
            else
            {
/*                if( file_exists($this->getOption('templateDir').$curTemplate) )
                {
print $this->getOption('templateDir').$curTemplate.' --- <br>';
                    $curTemplate = $this->getOption('templateDir').$curTemplate;
                }
                else*/
                    if( $this->getOption('fallbackLayout') )
                    {
                        $layoutTemplateFile = $this->getLayoutPath(false,true).$curTemplate;
                        if(file_exists($layoutTemplateFile))
                            $curTemplate = $layoutTemplateFile;
                    }
            }
        }
        else
        {
            $curTemplate = $this->contentTemplate;
//            if( substr($this->contentTemplate,0,1) != '/' )
            if( strpos($this->contentTemplate,$docRoot)!==0 )
                $curTemplate = '/'.$this->contentTemplate;
        }

//print $GLOBALS['DOCUMENT_ROOT'].' -<br>';
//print $this->options['templateDir'].$curTemplate.' --- <br>';
//print $curTemplate.' -- <br>';
        if( !file_exists($this->getOption('templateDir').$curTemplate) &&
            !file_exists($curTemplate)
          )
        {
            $file = $this->getDefaultContentFile();
            // subtract the php extension and add the template extension,
            $curTemplate = substr($file,0,-(strlen($this->options['phpExtension']))).$this->options['templateExtension'];
        }

        return $curTemplate;
    } // end of function


    /**
    *   is the current layout a framed-layout?
    *
    *   @access     public
    *   @version    2002/04/08
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @return     boolean
    */
    function isFramed()
    {
        if( strpos($this->getLayout(),'frame')!==FALSE )
        {
            return true;
        }
        return false;
    }


    function setCompiledHeaderTemplate( $file )
    {
        $this->_compiledHeaderTemplate = $file;
    }

    function setCompiledFooterTemplate( $file )
    {
        $this->_compiledFooterTemplate = $file;
    }

    function setCompiledHeadlineTemplate( $file )
    {
        $this->_compiledHeadlineTemplate = $file;
    }

    function setCompiledNavigationTemplate( $file )
    {
        $this->_compiledNavigationTemplate = $file;
    }

    function setCompiledNavigation1Template( $file )
    {
        $this->_compiledNavigation1Template = $file;
    }

    function setCompiledContentTemplate( $file )
    {
        $this->_compiledContentTemplate = $file;
    }

    function setCompiledContent1Template( $file )
    {
        $this->_compiledContent1Template = $file;
    }

    function setCompiledMainTemplate( $file )
    {
        $this->_compiledMainTemplate = $file;
    }

    function setCompiledTodoTemplate( $file )
    {
        $this->_compiledTodoTemplate = $file;
    }



    /**
    *   translates the file name into a virtual file name
    *
    *   @access     public
    *   @version    2002/04/08
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @return     boolean
    */
    function _makeVirtual( $file )
    {
        $from = preg_quote($this->getOption('templateDir'));
        $to = $this->getOption('virtualTemplateDir');
        return str_replace( $from , $to , $file );
    }

} // end of class
?>
