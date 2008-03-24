<?php
/**
*
*   $Log: Validation.php,v $
*   Revision 1.2  2003/03/11 12:57:56  wk
*   *** empty log message ***
*
*   Revision 1.9  2002/10/10 14:55:30  pp
*   - changed reg. expr. for email-check
*
*   Revision 1.8  2002/09/26 10:40:57  wk
*   - added checkMultiple - experimental for now!!!
*
*   Revision 1.7  2002/09/11 12:16:48  pp
*   - clean up code layout ;-)
*   - modified isHouseNumber for zero values
*
*   Revision 1.6  2002/05/27 20:20:24  wk
*   - corrected tabs
*
*   Revision 1.5  2002/05/17 19:32:05  wk
*   - added required parameters
*
*   Revision 1.4  2002/05/15 11:04:55  wk
*   - corrected package names
*
*   Revision 1.3  2002/05/06 16:10:16  wk
*   - bugfix in function isHouseNumber
*
*   Revision 1.2  2002/05/06 13:07:42  wk
*   - bugfixes
*
*   Revision 1.1  2002/05/03 14:31:23  wk
*   - new class
*
*/


/**
*
*
*   @package    vp_Validation
*   @version    2002/05/03
*   @access     public
*   @author     Nadja K�sters <nk@vision.de>
*/


class vp_Validation
{


    /**
    *    check for number
    *
    *   @version    2002/05/03
    *   @access     public
    *   @author     Nadja K�sters <nk@visionp.de>
    *   @param      string    string to check
    *   @return     boolean   true on success, or false if it could not be set
    */
    function isNumber( $var, $required=false )
    {
        if( $required && !vp_Validation::isMinLength( $var , 1 ) )
            return false;

        return is_numeric($var);

    }


    /**
    *    check for string
    *
    *   @version    2002/05/03
    *   @access     public
    *   @author     Nadja K�sters <nk@visionp.de>
    *   @param      string      string to check
    *   @param      boolean     if true a value has to be given
    *   @return     boolean     true on success, or false if it could not be set
    */
    function isString( $var, $required=false )
    {
        if( $required && !vp_Validation::isMinLength( $var, 1 ) )
            return false;

        if( settype($var, "string")!="0" )
        {
            return true;
        }
        else
        {
            return false;
        }
    }


    /**
    *    check for maxlength
    *
    *   @version    2002/05/03
    *   @access     public
    *   @author     Nadja K�sters <nk@visionp.de>
    *   @param      string    string to check
    *   @param      string    length of the string
    *   @return     boolean   true on success, or false if the length is wrong
    */
    function isMaxLength( $var, $length )
    {
        if( strlen($var)<=$length )
        {
            return true;
        }
        else
        {
            return false;
        }
    }


    /**
    *    check for minlength
    *
    *   @version    2002/05/03
    *   @access     public
    *   @author     Nadja K�sters <nk@visionp.de>
    *   @param      string    string to check
    *   @param      string    length of the string
    *   @return     boolean   true on success, or false if the length is wrong
    */
    function isMinLength( $var, $length )
    {
        if( strlen($var)>=$length )
        {
            return true;
        }
        else
        {
            return false;
        }
    }


    /**
    *    check  the zip
    *
    *   @version    2002/05/03
    *   @access     public
    *   @author     Nadja K�sters <nk@visionp.de>
    *   @param      string    string to check
    *   @param      string    length of the string
    *   @return     boolean   true on success, else false
    */
    function isZipCode( $var, $length=5 )
    {
        if( vp_Validation::isNumber($var) && vp_Validation::isMinLength($var, $length) &&
            vp_Validation::isMaxLength($var, $length) )
        {
            return true;
        }
        else
        {
            return false;
        }
    }


    /**
    *    check  the phone- or faxnumber
    *
    *   @version    2002/05/03
    *   @access     public
    *   @author     Nadja K�sters <nk@visionp.de>
    *   @param      string    string to check
    *   @param      string    regular expression
    *   @return     boolean   true on success, else false
    */
    function isPhoneFax( $var, $regex='', $required=false )
    {
        if( $required && !vp_Validation::isMinLength( $var, 1 ) )
            return false;

        if( eregi($regex, $var) )
        {
            return true;
        }
        else
        {
            return false;
        }
    }


    /**
    *    check the email
    *
    *   @version    2002/05/03
    *   @access     public
    *   @author     Nadja K�sters <nk@visionp.de>
    *   @param      string    string to check
    *   @return     boolean   true on success, else false
    */
    function isEmail( $var, $required=false )
    {
        if( $required && !vp_Validation::isMinLength( $var, 1 ) )
            return false;

        if( eregi("^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,4}$", $var) )
        {
            return true;
        }
        else
        {
            return false;
        }
    }


    /**
    *    check the house number
    *
    *   @version    2002/05/06
    *   @access     public
    *   @author     Nadja K�sters <nk@visionp.de>
    *   @param      string    string to check
    *   @return     boolean   true on success, else false
    */
    function isHouseNumber( $var, $required=false )
    {
        if( $required && !vp_Validation::isMinLength( $var, 1 ) )
            return false;

        if( eregi("^[1-9]?[0-9]+[a-z]?", $var) )
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
    *
    *   @param  array   this array contains multiple arrays where the values are the following
    *                   0=> data to check
    *                   1=> method to use for checking (i.e. 'isEmail', 'isZipCode', etc.)
    *                       if this is an array the first value is the method name, all the following are the paramters
    *                   2=> string to return in case of error
    *   @return mixed   array on errors, false otherwise
    */
    function checkMultiple( &$dataField , $checkData )
    {
        $ret = array();
        foreach( $checkData as $aData )
        {
            if( is_array($aData[1]) )
            {
                $method = $aData[1][0];
                array_shift($aData[1]);
//                if( !call_user_func_array( 'vp_Validation::'.$method , $aData[1] ) ) doesnt work for some reason :-(
                $evalString = 'vp_Validation::'.$method.'(';
                if(sizeof($aData[1]))
                    $evalString .= "'{$dataField[$aData[0]]}','".implode('\',\'',$aData[1]).'\'';   //"

                eval("\$result = $evalString);");
                if( !$result )
                    $ret[] = $aData[2];
            }
            else
            {
//print "vp_Validation::{$aData[1]}({$dataField[$aData[0]]})<br>";
                if( !vp_Validation::$aData[1]($dataField[$aData[0]]) )
                    $ret[] = $aData[2];
            }
        }
        return sizeof($ret)?$ret:false;
    }

}//end of class
?>
