<?php
//
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2002 The PHP Group                                |
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
//
/**
*
*   $Log: MemoryQuest.php,v $
*   Revision 1.3  2003/03/11 12:57:56  wk
*   *** empty log message ***
*
*   Revision 1.4  2002/03/18 22:01:57  mccain
*   - remove digest since it is not really needed here
*
*   Revision 1.3  2002/02/26 15:18:38  mccain
*   - removed unnecessary property declaration
*
*   Revision 1.2  2002/02/07 22:06:46  mccain
*   - added informational comment
*
*   Revision 1.1.1.1  2002/02/07 17:26:33  mccain
*
*   ##### those are my local revisions, from before moving it to sourceforge :-) #####
*   ##### just kept for informational reasons, might be removed one day
*
*   Revision 1.6  2002/01/21 23:01:53  cain
*   - added license statement
*
*   Revision 1.5  2001/10/30 21:54:52  cain
*   - decided to call the method which finally accesses the data source '_login' (before it was authenticate) so the association is easier
*
*   Revision 1.4  2001/10/29 23:11:55  cain
*   - added package name
*
*   Revision 1.3  2001/10/29 19:20:39  cain
*   - renamed files and includes to be PEAR-compatible (without 'Auth_' before)
*
*   Revision 1.2  2001/10/27 16:44:39  cain
*   - use proper error handling
*
*   Revision 1.1  2001/10/26 11:56:47  cain
*   *** empty log message ***
*
*
*/

require_once("Auth/Memory.php");

/**
*
*   @TODO
*       - add option matchMode 'sloppy', 'exact', but how can we add an option to the options property??? means extend the array, so setOption will work
*   @package    Auth
*
*/

class Auth_MemoryQuest extends Auth_Memory
{

    /**
    *
    *
    *   @var    string  map
    */
    var $map = array(   0=>'question',
                        1=>'answer');


    /**
    *
    *   @access public
    *   @author Wolfram Kriesing <wolfram@kriesing.de>
    */
    function Auth_MemoryQuest( $dsninfo )
    {
        $this->Auth_Memory( $dsninfo );
    }

    /**
    *   try to authenticate the user, comparing username and password
    *   with the given source, does the work for login
    *
    *   @author Wolfram Kriesing <wolfram@kriesing.de>
    *   @param  string  $username
    *   @param  string  $password
    *   @param  boolean true on success
    */
    function _login( $question , $answer )
    {
        $authenticated = false;

        if( Auth::isError($err = $this->openFile()) )
            return $err;

        // read from the given file
        while( $data = $this->readLine() )
        {
#printf("compare %s - %s<br>",strtolower($this->digest($question,$answer)) , strtolower(trim($data['answer'])));
            if( $question==$data['question'] &&
                strpos( strtolower($answer) , strtolower(trim($data['answer'])) )!==false )
            {
                $authenticated = true;
                break;
            }
        }
        $this->closeFile();

        if( !$authenticated )
            return AUTH_FAILED;

        return AUTH_OK;
    }

    /**
    *
    *
    * @param
    *
    * @return
    *
    */
    function getQuestion()
    {
        if( Auth::isError($err = $this->openFile()) )
            return $err;

        while( $data = $this->readLine() )
        {
            $questions[] = $data['question'];
        }
        $this->closeFile();

        shuffle($questions);
        return $questions[0];
    }


}
?>