<!--
    $Id
    
    Revision 1.22.2.3  2006/08/26 10:18:20  ak
    - make the url for logo in upper right configurable with config.php

    Revision 1.22.2.2  2003/03/28 10:18:20  wk
    - show the complete project name on mouseover, to make the full name available too

    Revision 1.22.2.1  2003/03/28 10:06:59  wk
    - truncate the project name when it gets too long

    Revision 1.22  2003/02/11 12:00:10  wk
    - add proper info to each time-slice

    Revision 1.21  2003/02/10 19:27:52  wk
    - use projectTreeDyn now

    Revision 1.20  2003/01/29 16:07:27  wk
    - make it translatable

    Revision 1.19  2003/01/29 10:40:06  wk
    - pass parameters as input-hidden

    Revision 1.18  2003/01/28 10:58:18  wk
    - use relative urls
    - make logo a link

    Revision 1.17  2002/11/30 18:39:39  wk
    - whitespace

    Revision 1.16  2002/11/30 14:33:13  wk
    - changed class

    Revision 1.15  2002/11/30 14:16:12  jv
    - really really the last change! promised! thanks for merge!!! -

    Revision 1.14  2002/11/30 13:56:22  jv
    - really the last change! promised! thanks for merge!!! -

    Revision 1.13  2002/11/30 13:05:32  wk
    - prepare a bit for translation
    - some reformatting

    Revision 1.12  2002/11/29 17:46:36  jv
    - change info on top again -

    Revision 1.11  2002/11/29 15:26:23  jv
    - change dateformat -

    Revision 1.10  2002/11/29 15:08:44  jv
    - change placement of elements and layout cahnges -

    Revision 1.9  2002/11/29 08:54:00  jv
    - add quick-bar with infos and shortcuts  -

    Revision 1.8  2002/11/26 16:00:07  wk
    - update today link

    Revision 1.7  2002/11/13 19:02:38  wk
    - show 'Admin mode' warning

    Revision 1.6  2002/10/31 17:48:47  wk
    - set class=button

    Revision 1.5  2002/10/24 14:15:20  wk
    - relayout

    Revision 1.4  2002/10/22 14:44:18  wk
    - changed $auth to $userAuth

    Revision 1.3  2002/09/23 09:35:12  wk
    - added shortcuts

    Revision 1.2  2002/07/24 17:10:54  wk
    - update headline to use tree

    Revision 1.1.1.1  2002/07/22 09:37:37  wk


//-->


<table width="100%" border="0" cellspacing="0" cellpadding="0" class="layoutWithBgColor">
    <tr>
        <td class="layoutWithBgColor" nowrap="nowrap" style="padding-left:10px;" valign="bottom">
            {if($userAuth->isLoggedIn())}
                <b>Hello {$userAuth->getData('name')}&nbsp;{$userAuth->getData('surname')}!</b><br>
                {if( $isAdmin )}
                    <font class="success">using Admin mode!</font>
                {else}
                    &nbsp;
        </td>

        {if($userAuth->isLoggedIn() && sizeof($today) > 0)}
            <td class="layoutWithBgColor" valign="bottom">
                <table class="layoutWithBgColor" align="left">
                    <tr>
                        <td class="layoutWithBgColor" nowrap>current date:</td>
                        <td class="layoutWithBgColor" nowrap>{$dateTime->formatDateFull(null)}</td>
                    </tr>
                    <tr>
                        <td class="layoutWithBgColor" nowrap>current project:</td>
                        <td class="layoutWithBgColor" nowrap>
                            <a class="noStyle" title="{$projectTreeDyn->getPathAsString($currentTask['projectTree_id'])}">
                                {$projectTreeDyn->getPathAsString($currentTask['projectTree_id'],50)}
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td class="layoutWithBgColor" nowrap>current task:</td>
                        <td class="layoutWithBgColor" nowrap>
                            {$currentTask['_task_name']}
                            - since&nbsp;{$dateTime->formatTimeShort($currentTask['timestamp'])}
                        </td>
                    </tr>
                </table>
            </td>

        <td class="layoutWithBgColor" align="right">
            <a href="{$config->logourl}" target="_blank">
            <img src="tt_logo.gif" border="0"></a>
        </td>
    </tr>

<!-- top statusleiste  -->   
    <tr>
        <td colspan="3" class="layoutWithBgColor" align="center">
            <table style="margin: 5px;" cellspacing="0" cellpadding="0" width="99%">
                <tr>
                    {if( $userAuth->isLoggedIn() )}
                        <td class="statusLine" align="left" nowrap>
                            <b>User</b>
                        </td>
                        <form action="{$config->applPathPrefix}/modules/user/login.php" method="post">
                            <input type="hidden" name="logout" value="1">
                            <td class="statusLine" align="left" nowrap>
                                <input type="submit" value="logout" class="button">
                            </td>
                        </form>

                    {if( sizeof($noneProjectTasks) )}
                        <form name="shortForm" action="{$config->applPathPrefix}/modules/time/shortcut.php" method="post" onSubmit="return confirmShort();">
                        <input type="hidden" name="shortoverBooked" id="shortoverBooked" value="0">
    					<input type="hidden" name="shortrestAvailable" id="shortrestAvailable" value="0">
    					<input type="hidden" name="currTask" id="currTask" value="0">
                        <td class="statusLine" align="left" nowrap>
                            <b>Hot-Keys</b>
                        </td>
                        <td class="statusLine" align="left" nowrap>
                            {foreach( $noneProjectTasks as $aNoneTask)}        
                                <input name="shortcutTaskId[{$aNoneTask['id']}]" type="submit" 
                                	value="{$aNoneTask['name']}" class="button" onclick="javascript:document.shortForm.currTask.value={$aNoneTask['id']}" />
                        </td>
                        </form>
                    
                    {if($userAuth->isLoggedIn() && sizeof($today) > 0)}
                        <td align="LEFT" class="statusLine" nowrap>
                            <b>Info</b>
                        </td>
                        
                        <td width="100%" align="LEFT" class="statusLine">
                            <table class="layoutWithBgColor" align="left">
                                <tr>
                                    <td class="layoutWithBgColor">
                                        <a href="{$config->applPathPrefix}/modules/time/today.php">today</a>
                                    </td>
                                    <td nowrap>
                                        {$dateTime->formatTimeShort($today[0]['timestamp'])}&nbsp;
                                        {foreach($today as $aTask)}
                                            <a title="{$aTask['_title']}">
                                            <span style="background-color:{$aTask['_task_color']}; border:0px;">
                                            <img src="pixel" width="{$time->getImgWidth($aTask)}" height="10" border="0">
                                            </span>
                                            </a>
                                        &nbsp;{$dateTime->formatTimeShort($aTask['timestamp'])}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    {else}
                        <td width="100%" align="LEFT" class="statusLine">
                            &nbsp;
                        </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<script type="text/javascript" language="JavaScript">
    function confirmShort()
    \{
    	var currentTime = new Date();
    	var month = currentTime.getMonth() + 1;
    	var day = currentTime.getDay();
    	var year = currentTime.getFullYear();
    	var hours = currentTime.getHours();
    	var minutes = currentTime.getMinutes();
    	if (minutes < 10) \{
    		minutes = "0" + minutes;
    	\}
      
		bookDate = day+'.'+month+'.'.year;
		bookTime = hours + ":" + minutes;
		taskId = document.shortForm.currTask.value;
		projectTreeId = "";
		oldid = 0;
		
		xajax.call( 'checkBookings', \{ mode:'synchronous', parameters:[projectTreeId,taskId,oldid,bookDate,bookTime,'short'] \} );

		if(document.shortForm["shortoverBooked"].value && document.shortForm["shortoverBooked"].value != "0") \{
			neg = document.shortForm["shortrestAvailable"].value.indexOf('-');
			if(neg != -1) \{
			    overbooked = document.shortForm["shortrestAvailable"].value.substr(1);
			    message = "{$T_MSG_PROJECT_OVERBOOKED}"+"\n{$T_MSG_PROJECT_BOOKING_CHOICE_QUESTION}\n\n{$T_MSG_PROJECT_BOOKING_CHOICE_CANCEL}\n{$T_MSG_PROJECT_BOOKING_CHOICE_OK}\n";
			\} else \{
			    message = "{$T_MSG_PROJECT_OVERBOOKED21}"+document.shortForm["shortrestAvailable"].value+" "+"{$T_MSG_PROJECT_OVERBOOKED22}\n{$T_MSG_PROJECT_BOOKING_CHOICE_QUESTION}\n\n{$T_MSG_PROJECT_BOOKING_CHOICE_CANCEL}\n{$T_MSG_PROJECT_BOOKING_CHOICE_OK}\n";
			\}
	        return confirm(message);	  
		\} else \{
		    return true;
		\}
    \}
</script>