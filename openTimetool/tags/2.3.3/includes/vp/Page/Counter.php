<?php
//
//  $Log: Counter.php,v $
//  Revision 1.2  2003/03/11 12:57:56  wk
//  *** empty log message ***
//
//  Revision 1.1  2002/06/19 15:24:58  wk
//  - first checkin for vp
//
//

require_once('vp/CommonDB.php');

/**
*   PRE-ALPHA -version !!!!!!
*
*   @package  vp_Page
*   @access   public
*   @author   Wolfram Kriesing <wolfram@kriesing.de>
*   @version  01/15/2002
*/

class vp_Page_Counter extends vp_CommonDB
{

    var $options = array(   'table' =>  '');

    /**
    *
    *
    * @version    2000/10/
    *
    * @author     Wolfram Kriesing <wolfram@kriesing.de>
    *
    * @param
    * @return
    *
    */
    function countPage($fileName,$info="",$category="")
    {

        $nextId = $this->dbh->nextId($this->getOption('table'));
        $query = sprintf("INSERT INTO %s (id,requestedFile,ip,userAgent,refer) VALUES (%s,'%s','%s','%s','%s')",
                            $this->getOption('table'),
                            $nextId,
                            addslashes($fileName),
                            $GLOBALS['HTTP_X_FORWARDED_FOR'] ? $GLOBALS['HTTP_X_FORWARDED_FOR'] : $GLOBALS['REMOTE_ADDR'],
                            addslashes($GLOBALS['HTTP_USER_AGENT']),
                            addslashes($GLOBALS['HTTP_REFERER'])
#                            addslashes($info),
#                            addslashes($category)
                        );

        // register the user and get some infos about him
        $res = $this->dbh->query( $query );
        if( DB::isError( $res ) )
        {
            echo "vp_Page_Counter::countPage - error logging here  msg:".$res->message." - $query<br>";
            return false;
        }
        return true;
    } // end of function

    /**
    *
    *
    * @version    2000/10/
    *
    * @author     Wolfram Kriesing <wolfram@kriesing.de>
    *
    * @param
    * @param
    * @return
    *
    */
/*    function getCount($page="%/homepage/index.php3")
    {
    global $db;

    if( $db->query("select count(*) as count from kriesing_de_stats where page like '%homepage/index.php3'") && $db->next_record() )
    {
        return($db->Record["count"]);
    }
    return;
    } // end of function
*/
} // end of class
?>