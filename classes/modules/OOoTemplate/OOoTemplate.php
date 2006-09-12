<?php
//
//  $Log: OOoTemplate.php,v $
//  Revision 1.1  2002/11/11 17:55:26  wk
//  - initial commit
//
//

require_once($config->classPath.'/modules/common.php');

/**
*
*
*   @package    modules
*   @version    2002/11/08
*   @access     public
*   @author     Wolfram Kriesing <wolfram@kriesing.de>
*/
class modules_OOoTemplate extends modules_common
{

    var $table = TABLE_OOOTEMPLATE;

    /**
    *
    *   @version    2002/11/08
    */
    function modules_OOoTemplate()
    {
        parent::modules_common();
        $this->preset();
    }

    /**
    *   init the table settings, by default we dont want the blob data
    *
    *   @version    2002/11/08
    *   @access     public
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    */
    function preset()
    {
        $this->reset();
        $this->setDontSelect('data');  // dont get all the blob data here, by default. if u need them select them explicitly
    }

    /**
    *   add the timestamp, so its saved in the db too
    *
    *   @version    2002/11/08
    *   @access     public
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      array   all the data
    *   @return     mixed   either false, or the id of the inserted item
    */
    function add( $data )
    {
        $data['timestamp'] = time();
        return parent::add($data);
    }

    /**
    *   this gets the filename for the given id and puts the data from the db in the tmp-dir
    *   if the template for the id is not in the filesystem yet it gets the blob data
    *   and saves them as a template file in the tmp-dir
    *
    *   @version    2002/11/08
    *   @access     public
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      int     the id of the template
    *   @return     string  the complete path name to the template
    */
    function getFilename( $id )
    {
        global $applError,$config;
        
        if( !$id )
            return false;
                        
        $this->preset();
        if( $template = $this->get($id) )
        {            
            // we hash the name, because there can be any kind of letter in the 'name'
            // which might produce an invalid filename ...
            $filename = $config->OOoTemplateDir.'/'.md5($template['name'].$template['timestamp']).'.'.$template['type'];
            if( @is_file($filename) )
            {
                return $filename;
            }
            if( !@is_dir($config->OOoTemplateDir) )
            {
                if( !@mkdir($config->OOoTemplateDir,0777) )
                {
                    $applError->set('Could not write into the OpenOffice-Template directory!');
                    return false;
                }
            }
                                              
            if( $templateData = $this->get($id,'data') )
            {
                if( $fp = fopen($filename,'w') )
                {
                    fwrite($fp,$templateData);
                    fclose($fp);
                }
                if( @is_file($filename) )
                {
                    return $filename;
                }
                else
                {
                    $applError->set('Error retreiving the template, please try again!');
                    return false;
                }
            }
        }
        return false;
    }

    /**
    *   this puts the file with the given id to the browser
    *   NOTE: this method puts the file out to the browser
    *   your script should die after this to not modify the data!
    *
    *   @version    2002/11/08
    *   @access     public
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      int     the id of the template
    *   @return     string  the complete path name to the template
    */
    function putFile( $id )
    {
        global $config;
                    
        $this->preset();
        $type = $this->get($id,'type');

        require_once('vp/Util/MimeType.php');
        header('Content-type: '.vp_Util_MimeType::getMimeType($type));

        $filename = $this->getFilename($id);

        readfile($filename);
    }


}   // end of class

$OOoTemplate = new modules_OOoTemplate;

?>
