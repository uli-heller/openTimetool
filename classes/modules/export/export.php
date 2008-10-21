<?php
/**
 *  $Id
 * 
 * Export functionality called by time/index.php (as include)
 * It is also included in htdocs/modules/export.php : the export page
 * 
 * ********** switch to SVN ********
 *  $Log: export.php,v $
 *  Revision 1.8  2003/03/04 19:07:39  wk
 *  - merge in bugfix from 1.0.5 for IE
 *
 *  Revision 1.7  2003/02/14 15:39:11  wk
 *  - handle csv exports
 *  - CS
 *
 *  Revision 1.6  2002/12/09 12:19:44  wk
 *  - implement the 'download' parameter in the putFile method
 *
 *  Revision 1.5  2002/12/05 14:17:49  wk
 *  - make it downloadable
 *
 *  Revision 1.4  2002/11/20 20:09:06  wk
 *  - show and save only the current user's exported files
 *
 *  Revision 1.3  2002/11/12 15:29:07  wk
 *  - added some comments
 *
 *  Revision 1.2  2002/11/12 13:09:41  wk
 *  - do a left join
 *
 *  Revision 1.1  2002/11/11 17:55:26  wk
 *  - initial commit
 *
 */

require_once($config->classPath.'/modules/common.php');

/**
*
*
*   @package    modules
*   @version    2002/11/08
*   @access     public
*   @author     Wolfram Kriesing <wolfram@kriesing.de>
*/
class modules_export extends modules_common
{

    var $table = TABLE_EXPORTED;

    /**
    *
    *   @version    2002/11/08
    */
    function modules_export()
    {
        parent::modules_common();
        $this->preset();
    }

    /**
    *
    *   @version    2002/11/08
    */
    function preset()
    {                                       
        global $userAuth;

        $this->reset();
        // do left join, so we also get those which have no relation to the TABLE_OOOTEMPLATE
        // like html or pdf's
        $this->setLeftJoin(TABLE_OOOTEMPLATE,TABLE_OOOTEMPLATE.'.id=OOoTemplate_id');
        $this->setDontSelect(TABLE_OOOTEMPLATE.'.data'); // dont get the blob data!
        $this->setWhere('user_id='.$userAuth->getData('id')); // show only the files this user has exported
        $this->setOrder('timestamp',true);
    }

    /**
    *
    *   @version    2002/11/08                  
    *   @param  string  the source file, which will be *moved*
    *   @param  int     the OOoTemplate-id, 0 for not-OOo files
    */
    function saveFile( $filename , $templateId=0 )
    {
        global $config,$applError;      
     
        // we handle xls.csv and ods.csv differently
        $ext = substr($filename,-7);
        if ($ext!='sxc.csv' && $ext!='xls.csv' && $ext!='ods.csv') {
            $fileinfo = pathinfo($filename);
            $ext = $fileinfo['extension'];
        }

        //
        // copy the file into the exported-directory (tmp/_exportDir)
        //
        // create a unique name, since a user (we user his session-id) simply
        // cant export 2 files at exactly the same time (by using 'time()' in the hash)
        // the filename is 99.9% unique :-)
        $saveFilename = md5(time().$ext.session_id());

        if (!@is_dir($config->exportDir)) {
            if (!mkdir($config->exportDir,0777)) {
                $applError->set('Could not make directory \''.$config->exportDir.'\'!');
                return false;
            }
        }

                                                                            
        if( !is_file($filename) ||
            !@rename( $filename , $config->exportDir."/$saveFilename.$ext" ) )
        {
            $applError->set("Error saving file in export directory ('$filename')!");
            return false;
        }

        //
        // save the file in the DB, so the user can reget it
        //
        $data = array(  'filename'  =>  $saveFilename,
                        'type'      =>  $ext,
                        'OOoTemplate_id'=>$templateId);
        if( !$id = $this->add( $data ) )
        {
            $applError->log('export::saveFile - Error adding savedFile to DB '.$saveFilename);
        }

        return $id;
    }

    /**
    *   add the timestamp, so its saved in the db too  
    *
    *   @version    2002/11/08
    */
    function add( $data )
    {                      
        global $userAuth;

        $data['timestamp'] = time();
        $data['user_id'] = $userAuth->getData('id');
        return parent::add($data);
    }
      
    /**
    *   @param  string  the source file, which will be *moved*
    *   @param  int     the OOoTemplate-id, 0 for not-OOo files
    */
    function saveAndPutFile( $filename , $templateId=0 )
    {
        if( $id = $this->saveFile( $filename , $templateId ) )
        {
            $this->putFile( $id );
        }
    }

    /**
    *   this puts the file with the given id to the browser
    *   NOTE: this method puts the file out to the browser
    *   your script should die after this to not modify the data!
    *
    *   @version    2002/11/08
    *   @access     public
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      int     the id of the exported file
    */
    function putFile( $id , $download=false )
    {
        global $config, $dateTime;

        if( !$exported = $this->get($id) )
            return false;

        $filename = $config->exportDir.'/'.$exported['filename'].'.'.$exported['type'];

        include_once 'vp/Util/MimeType.php';
        $mimeType = vp_Util_MimeType::getMimeType($exported['type']);

        header('Content-type: '.$mimeType);
        header('Content-length: '.filesize($filename));
        // this is only for IE, there is a bug since v4.01
        // see http://support.microsoft.com/default.aspx?scid=KB;en-us;q231296
        header('Pragma: cache');
        header('Cache-Control: max-age=1');
        
        $showFilename = 'timetool-export-'.$dateTime->formatDate($exported['timestamp']).'.'.$exported['type'];
        if( !$download )
        {
            header('Content-Disposition: inline; filename="'.$showFilename.'"');
        }
        else
        {
            header('Content-Disposition:attachment; filename="'.$showFilename.'"');
        }


        readfile($filename);
    }

}   // end of class

$export = new modules_export;

?>
