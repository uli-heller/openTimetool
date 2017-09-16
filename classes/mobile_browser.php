<?php
/**
 * Created on 18.02.2010
 * 
 * SX : simple function to check if we have mobile browser
 * 
 * $Id$
 */

/**
 * just feed it with $_SERVER['HTTP_USER_AGENT']
 */
function is_mobile($browser)
{
    $ispda = false;

    if (@strpos($browser['browser_name_regex'],'ce')!=false){$ispda=true;}
    if (@strpos($browser['browser_name_pattern'],'CE')!=false){$ispda=true;}
    if (@strpos($browser['parent'],'Pocket')!=false){$ispda=true;}
    if (@strpos($browser['platform'],'WinCE')!=false){$ispda=true;}
    if (@strpos($browser['browser'],'Pocket')!=false){$ispda=true;}
    if (strpos($_SERVER['HTTP_USER_AGENT'],'PPC')!=false){$ispda=true;}
    if (strpos($_SERVER['HTTP_USER_AGENT'],'XDA')!=false){$ispda=true;}
    if (strpos($_SERVER['HTTP_USER_AGENT'],'CE;')!=false){$ispda=true;}
    if (strpos($_SERVER['HTTP_USER_AGENT'],'Smartphone')!=false){$ispda=true;}
    if (strpos($_SERVER['HTTP_USER_AGENT'],'SymbianOS')!=false){$ispda=true;}
    if (strpos($_SERVER['HTTP_USER_AGENT'],'SV1')!=false){$ispda=true;}
    if (strpos($_SERVER['HTTP_USER_AGENT'],'BlackBerry')!=false){$ispda=true;}
    if (strpos($_SERVER['HTTP_USER_AGENT'],'Nokia')!=false){$ispda=true;}
    if (strpos($_SERVER['HTTP_USER_AGENT'],'MSN Mobile Proxy')!=false){$ispda=true;}
    if (strpos($_SERVER['HTTP_USER_AGENT'],'AvantGo')!=false){$ispda=true;}
    if (strpos($_SERVER['HTTP_USER_AGENT'],'DoCoMo')!=false){$ispda=true;}
    if (strpos($_SERVER['HTTP_USER_AGENT'],'Opera Mini')!=false){$ispda=true;}
    if (strpos($_SERVER['HTTP_USER_AGENT'],'320x320')!=false){$ispda=true;}
    if (strpos($_SERVER['HTTP_USER_AGENT'],'Palm')!=false){$ispda=true;}
    if (strpos($_SERVER['HTTP_USER_AGENT'],'Mobile')!=false){$ispda=true;}
    if (strpos($_SERVER['HTTP_USER_AGENT'],'RegKing')!=false){$ispda=true;}
    if (strpos($_SERVER['HTTP_USER_AGENT'],'EPOC')!=false){$ispda=true;}
    if (strpos($_SERVER['HTTP_USER_AGENT'],'SAGEM')!=false){$ispda=true;}
    if (strpos($_SERVER['HTTP_USER_AGENT'],'SAMSUNG')!=false){$ispda=true;}
    if (strpos($_SERVER['HTTP_USER_AGENT'],'Novarra')!=false){$ispda=true;}

    return $ispda;
}
