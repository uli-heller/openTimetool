<?php
//
//  $Log: OptionsDB.php,v $
//  Revision 1.2  2003/03/11 12:57:55  wk
//  *** empty log message ***
//
//  Revision 1.2  2003/01/17 18:47:32  wk
//  - use xipe now!
//
//  Revision 1.1  2002/06/19 15:24:58  wk
//  - first checkin for vp
//
//

require_once('HTML/Template/Xipe/Options.php');

/**
*   this class additionally retreives a DB connection and saves it
*   in the property "_db"
*
*   @package  vp
*   @access   public
*   @author   Wolfram Kriesing <wolfram@kriesing.de>
*
*/
class vp_OptionsDB extends HTML_Template_Xipe_Options
{
    /**
    *   @var    object
    */
    var $_db;

    /**
    *   this constructor sets the options, since i normally need this and
    *   in case the constructor doesnt need to do anymore i already have it done :-)
    *
    *   @version    02/01/08
    *   @access     public
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      boolean true if loggedIn
    */
    function vp_OptionsDB( $dsn , $options=array() )
    {
        $res = $this->_connectDB( $dsn );
        if( !PEAR::isError($res) )
        {
            $this->_db->setFetchmode(DB_FETCHMODE_ASSOC);
            $this->HTML_Template_Xipe_Options( $this->_db->dsn['parameters'] );   // save the parameters in the options too
        }
        else
        {
            return $res;
        }

        $this->HTML_Template_Xipe_Options( $options );          // do options afterwards since it overrules
    }

    /**
     * Connect to database by using the given DSN string
     *
     * @author  copied from PEAR::Auth, Martin Jansen, slightly modified
     * @access private
     * @param  string DSN string
     * @return mixed  Object on error, otherwise bool
     */
    function _connectDB( $dsn )
    {
        if (is_string($dsn) || is_array($dsn) )
        {
            // put the dsn parameters in an array
            // DB would be confused with an additional URL-queries, like ?table=...
            // so we do it before connecting to the DB
            if( is_string($dsn) )
                $dsn = $this->parseDSN( $dsn );

            $this->_db = DB::Connect($dsn);
        }
        else
        if(get_parent_class($dsn) == "db_common")
        {
            $this->_db = $dsn;
        }
        else
        if (is_object($dsn) && DB::isError($dsn))
        {
            return new DB_Error($dsn->code, PEAR_ERROR_DIE);
        }
        else
        {
            return new PEAR_Error("The given dsn was not valid in file " . __FILE__ . " at line " . __LINE__,
                        41,
                        PEAR_ERROR_RETURN,
                        null,
                        null
                        );

        }

        if (DB::isError($this->_db))
            return new DB_Error($this->_db->code, PEAR_ERROR_DIE);

        return true;
    }

} // end of class
?>
