<!--

$Id$

-->

{%include common/macro/common.mcr%}
{%include vp/Application/HTML/Macro/Error.mcr%}

{include($layout->getHeaderTemplate(true))}

<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <thead>
        <tr>
            <td colspan="5" valign="top" class="layoutWithBgColor">
                {include($layout->getHeadlineTemplate(true))}
            </td>
        </tr>
    </thead>

    <tfoot>
        <tr>
            <td colspan="5" class="layoutWithBgColor" align="center">
                <div class="table statusLine foot">
                    <div class="tr">
                        <div class="td">
                            {$config->applName}
                            &nbsp;<i>{$account->isAspVersion()?'asp':'net'}</i>
                            {if($config->demoMode)}
                                <i>/demo</i>
                        </div>
                        {if(!is_array($user->canBeAdmin())&&$user->canBeAdmin()!==false)}
                            <div class="td">
                                licensed for: {$session->account->numUsers} user
                            </div>
<!-- we do that later!!! we got enough work now :-)
                            <div class="td">
                                current no. of users: X
                            </div>
-->
                        {if($account->isAspVersion())}
                            <div class="td">
                                account name: {$account->getAccountName()}
                            </div>
<!--
                        {if($session->account->features)}
                            <div class="td">
                                activated modules:
                                {echo implode(' | ',$session->account->features)}
                            </div>
-->
                        {if(@$session->account->expires)}
                            <div class="td">
                                your account expires: {$dateTime->formatDate($session->account->expires)}
                            </div>

                        <div class="td">
                            authentication mode: {$config->auth->method}
                        </div>

                        {if($userAuth->isLoggedIn())}
                            <div class="td" id="logoutText">
                                auto-logout in ...
                            </div>
                    </div>
                </div>
            </td>
        </tr>
    </tfoot>

    <tbody>
        <tr>
            <td class="layoutWithBgColor" width="10">{$utilHtml->getSpacer(10)}</td> <!-- space column -->
            <td class="layoutWithBgColor" width="117">{$utilHtml->getSpacer(117)}</td> <!-- the navi column -->
            <!--<td>{$utilHtml->getSpacer(16)}</td> <!-- space for the design-dotted line -->
            <td width="15">{$utilHtml->getSpacer(15)}</td> <!-- spacer between the text and the left side -->
            <td width="100%">&nbsp;</td> <!-- content space -->
            <td width="15">{$utilHtml->getSpacer(15)}</td>
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
                        <thead>
                            <tr>
                                <td class="pageHeader" width="99%">{$T_pageProp->get('pageHeader')}</td>
                                <td class="pageHeader" nowrap="nowrap" align="right">
                                    {%common_help()%}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">&nbsp;</td>
                            </tr>
                        </thead>

                    <tbody>
                        <tr>
                            <td colspan="2">
                                {%common_showError( $config )%}
                                {include($layout->getContentTemplate('',true))}
                                <br>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>

            <td>&nbsp;</td>    
<!--
            <td valign="top"><img src="dashedRedVertical.gif" width="16" height="257" alt=""><br></td>
-->
        </tr>

        <tr>
            <td colspan="2" align="right" valign="bottom" class="layoutWithBgColor"><img src="dashedGreyHorizonal1.gif" width="137" height="19" alt="">
            </td>

            <td colspan="3" valign="bottom" class="layout"><img src="dashedGreyHorizonal2.gif" width="58" height="19" alt="">
            </td>
        </tr>
    </tbody>
</table>

<script>
    var _countDownTime = {$userAuth->options['expire']};

    var timeNow = new Date();
    var _startCountDownTime = timeNow.getTime() / 1000;

    function updateLogoutCountDown()
    \{
        var timeNow = new Date();
        var _now = timeNow.getTime() / 1000;

        timeLeft = _countDownTime-(_now-_startCountDownTime);
        _hours = parseInt(timeLeft/3600);
        _minutes = parseInt((timeLeft-(_hours*3600))/60);
        _seconds = parseInt(timeLeft%60);
        showTimeLeft = (_hours<10?"0":"")+_hours +":"+
                       (_minutes<10?"0":"")+_minutes+":"+
                       (_seconds<10?"0":"")+_seconds;

        document.getElementById("logoutText").firstChild.data = "auto-logout in "+showTimeLeft+" h";
        if (timeLeft>0) \{
            window.setTimeout("updateLogoutCountDown()", 1000);
        \} else \{
            document.getElementById("logoutText").firstChild.data = "... AUTO LOGOUT NOW ...";
        \}
    \}

    {if ($userAuth->isLoggedIn())}    
        updateLogoutCountDown();
</script>

{include($layout->getFooterTemplate(true))}
