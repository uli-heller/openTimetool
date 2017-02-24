<!--
    $Log: main.tpl,v $
    Revision 1.15  2003/03/04 19:18:52  wk
    - show logout countdown

    Revision 1.14  2003/01/29 10:39:48  wk
    - E_ALL stuff

    Revision 1.13  2002/12/13 10:41:45  wk
    - changed string 'authentication mode:'

    Revision 1.12  2002/12/10 18:11:07  wk
    - show auth-method

    Revision 1.11  2002/11/30 18:39:50  wk
    - use layout* class

    Revision 1.10  2002/11/30 13:05:47  wk
    - prepare a bit for translation
    - some reformatting

    Revision 1.9  2002/11/29 14:45:44  jv
    - minimal layout changes  -

    Revision 1.8  2002/11/29 08:53:25  jv
    - small changes in statusline  -

    Revision 1.7  2002/11/28 10:30:56  wk
    - added status line

    Revision 1.6  2002/11/22 20:14:30  wk
    - use common_showError

    Revision 1.5  2002/11/20 20:10:18  wk
    - translate explicitly

    Revision 1.4  2002/11/11 17:59:38  wk
    - show errors properly

    Revision 1.3  2002/10/22 14:44:30  wk
    - show page header

    Revision 1.2  2002/09/11 15:51:01  wk
    - renamed pics to have proper names

    Revision 1.1.1.1  2002/07/22 09:37:37  wk


-->

{%include common/macro/common.mcr%}
{%include vp/Application/HTML/Macro/Error.mcr%}


{include($layout->getHeaderTemplate(true))}

<!-- this should work too :-) for creating some space at the top of the site -->
<br><br>

<center>
<table width="95%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td colspan="6" valign="top" class="layoutWithBgColor">
            {include($layout->getHeadlineTemplate(true))}
        </td>
    </tr>

    <tr>
        <td class="layoutWithBgColor" width="10">{$utilHtml->getSpacer(10)}</td>     <!-- space column -->
        <td class="layoutWithBgColor" width="117">{$utilHtml->getSpacer(117)}</td>    <!-- the navi column -->
        <!--<td>{$utilHtml->getSpacer(16)}</td>     <!-- space for the design-dotted line -->
        <td>{$utilHtml->getSpacer(15)}</td>     <!-- spacer between the text and the left side -->
        <td width="100%">&nbsp;</td>    <!-- content space -->
        <td>{$utilHtml->getSpacer(15)}</td>
    </tr>

    <tr>
        <td class="layoutWithBgColor">&nbsp;<br></td>
        <td valign="top" class="layoutWithBgColor">
            {include($layout->getNavigationTemplate(true))}
        </td>

        <td>&nbsp;<br></td>

        <td valign="top">

            <table width="100%">
                {if($pageProp->get('pageHeader'))}
                    <tr>
                        <td class="pageHeader" width="99%">{$T_pageProp->get('pageHeader')}</td>
                        <td class="pageHeader" nowrap="nowrap" align="right">
                            {%common_help()%}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">&nbsp;</td>
                    </tr>
                <tr>
                    <td colspan="2">
                        {%common_showError( $config )%}
                        {include($layout->getContentTemplate('',true))}
                        <br>
                    </td>
                </tr>
            </table>


        </td>
        
        <td>&nbsp;</td>    
<!--
        <td valign="top"><img src="dashedRedVertical" alt="" border="0"><br></td>
-->

    </tr>

    <tr>
        <td colspan="2" class="layoutWithBgColor" valign="bottom"><img src="dashedGreyHorizonal1" alt="" border="0">
        </td>

        <td colspan="4" valign="bottom" class="layout"><img src="dashedGreyHorizonal2" alt="" border="0">
        </td>
    </tr>

    <tr>
        <td colspan="6" class="layoutWithBgColor" align="center">
            <table style="margin: 5px;" cellspacing="0" cellpadding="0" width="99%">
                <tr>
                    <td class="statusLine" align="center">
                        {$config->applName}
                        &nbsp;<i>{$account->isAspVersion()?'asp':'net'}</i>
                    </td>
                    {if( $user->canBeAdmin() )}
                        <td class="statusLine" align="center">
                            licensed for: {$session->account->numUsers} user
                        </td>
<!-- we do that later!!! we got enough work now :-)
                        <td class="statusLine" align="center">
                            current no. of users: X
                        </td>
-->
                    {if(  $account->isAspVersion())}
                        <td class="statusLine" align="center">
                            account name: {$account->getAccountName()}
                        </td>
<!--
                    {if($session->account->features)}
                        <td class="statusLine" align="center">
                            activated modules:
                            {echo implode(' | ',$session->account->features)}
                        </td>
-->
                    {if( @$session->account->expires )}
                        <td class="statusLine" align="center">
                            your account expires: {$dateTime->formatDate( $session->account->expires )}
                        </td>

                    <td class="statusLine" align="center">
                        authentication mode: {$config->auth->method}
                    </td>

                    {if ($userAuth->isLoggedIn())}
                        <td class="statusLine" align="center" id="logoutText">
                            auto-logout in ...
                        </td>

                </tr>
            </table>
        </td>
    </tr>

</table>
</center>
{include($layout->getFooterTemplate(true))}

<script>

    var _countDownTime = {$userAuth->options['expire']};
    
    var timeNow = new Date();
    var _startCountDownTime = timeNow.getTime()/1000;
    
    function updateLogoutCountDown()
    \{
        var timeNow = new Date();
        var _now = timeNow.getTime()/1000;
        
        timeLeft = _countDownTime-(_now-_startCountDownTime);
        _hours = parseInt(timeLeft/3600);
        _minutes = parseInt((timeLeft-(_hours*3600))/60);
        _seconds = parseInt(timeLeft%60);
        showTimeLeft =  (_hours<10?"0":"")+_hours +":"+ 
                        (_minutes<10?"0":"")+_minutes+":"+
                        (_seconds<10?"0":"")+_seconds;
        
        document.getElementById("logoutText").firstChild.data = "auto-logout in "+showTimeLeft+" h";
        if (timeLeft>0)
            window.setTimeout("updateLogoutCountDown()",1000);
        else \{
            document.getElementById("logoutText").firstChild.data = "... AUTO LOGOUT NOW ...";
        \}
    \}

    {if ($userAuth->isLoggedIn())}    
        updateLogoutCountDown();
    
</script>

