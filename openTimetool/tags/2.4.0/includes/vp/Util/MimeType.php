<?php
/**
 * $Id
 * 
 * 
 * ******** switch to SVN *******
 *  $Log: MimeType.php,v $
 *  Revision 1.2  2003/03/11 12:57:56  wk
 *  *** empty log message ***
 *
 *  Revision 1.3  2003/02/14 15:36:56  wk
 *  - add 2 new mime types
 *
 *  Revision 1.2  2002/11/19 19:54:48  wk
 *  - added pdf
 *
 *  Revision 1.1  2002/11/11 17:53:30  wk
 *  - initial commit
 *
 */

class vp_Util_MimeType
{            
	// AK : added OO 2.0 mimetypes
	// sxw & sxc are OO1.x; odt&ods are OO2.x
	// AK : with 2.2.8 we skip the old OO 1.x-stuff now
	// but keep that mime type stuff for compatibility
	// It's enough to delete the table row
    var $_map = array(
                        'sxw'   =>  array(  'mimeType'  =>  'application/vnd.sun.xml.writer',
                                            'name'      =>  'OpenOffice.org - Writer') ,
                        'odt'  =>  array(  'mimeType'  =>  'application/vnd.oasis.opendocument.text',
                        					'name'		 =>  'OpenOffice.org - 2.0 - Writer')
                        ,'sxc'  =>  array(  'mimeType'  =>  'application/vnd.sun.xml.calc',
                                            'name'      =>  'OpenOffice.org - Calc')
                        ,'ods'  =>  array(  'mimeType'  =>  'application/vnd.oasis.opendocument.spreadsheet',
                                            'name'      =>  'OpenOffice.org - 2.0 - Calc')                                            
                        ,'pdf'  =>  array(  'mimeType'  =>  'application/pdf',
                                            'name'      =>  'Acrobat Reader')
                        ,'sxc.csv'=>array(  'mimeType'  =>  'application/vnd.sun.xml.calc',
                                            'name'      =>  'OpenOffice.org - Calc')
                        ,'ods.csv'=>array(  'mimeType'  =>  'application/vnd.oasis.opendocument.spreadsheet',
                                            'name'      =>  'OpenOffice.org - 2.0 - Calc')                                            
                        ,'xls.csv'=>array(  'mimeType'  =>  'application/vnd.ms-excel',
                                            'name'      =>  'Microsoft - Excel')
                    );

    /**
    *   get the default extension for a given mimetype
    *
    *   @version    2002/11/08
    *   @access     public
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  the mime-type
    *   @return     string  the extension
    */
    function getExtension( $mimeType )
    {
        $mimes = new vp_Util_MimeType();    // make an instance so we can access the class-vars, PHP4
        return $mimes->getByMimeType($mimeType,'extension');
    }

    /**
    *   get the mimetype for a given default extension
    *
    *   @version    2002/11/08
    *   @access     public
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  the extension
    *   @return     string  the mime-type
    */
    function getMimeType( $extension )
    {
        $mimes = new vp_Util_MimeType();    // make an instance so we can access the class-vars, PHP4
        return $mimes->getByExtension($extension,'mimeType');
    }

    function getByExtension( $ext , $value=null )
    {
        $mimes = new vp_Util_MimeType();    // make an instance so we can access the class-vars, PHP4
        $map = $mimes->getMap();
        if( $value )
            return $map[$ext][$value];
        else
            return $map[$ext];
    }

    function getByMimeType( $mime , $value=null )
    {
        $mimes = new vp_Util_MimeType();    // make an instance so we can access the class-vars, PHP4
        $map = $mimes->getMap(true);
        if( $value )
            return $map[$mime][$value];
        else
            return $map[$mime];
    }

    /**
    *   get the entire mapping table
    *
    *   @version    2002/11/08
    *   @access     public
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      boolean if true the array is flipped, so that mimeType is the index for the extension
    *   @return     array   the map
    */
    function getMap( $flip=false )
    {          
        if( $flip )
        {               
            $new = array();
            foreach( $this->_map as $ext=>$data )
            {
                $new[$data['mimeType']] = array_merge( $data , array('extension'=>$ext) );
            }
            return $new;
        }
        else
            return $this->_map;
    }
}

?>
