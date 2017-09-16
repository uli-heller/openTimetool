<!--

$Id$

-->

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="layoutWithBgColor">
    <tr>
        <td class="layoutWithBgColor" nowrap="nowrap" style="padding-left:10px;" valign="bottom">
        {if($userAuth->isLoggedIn())}
            <b>Hello {$userAuth->getData('name')}&nbsp;{$userAuth->getData('surname')}!</b><br>
            {if( $isAdmin )}
                <span class="success">using Admin mode!</span> &middot;
                <a href="{$config->applPathPrefix}/modules/system/" title="openTimetool System Menu">TSM</a>
            {else}
                &nbsp;
        </td>

        {if($userAuth->isLoggedIn() && sizeof($today) > 0)}
            <td class="layoutWithBgColor" valign="bottom">
                <table class="layoutWithBgColor" align="left">
                    <tr>
                        <td class="layoutWithBgColor" nowrap="nowrap">current date:</td>
                        <td class="layoutWithBgColor" nowrap="nowrap">{$dateTime->formatDateFull(null)}</td>
                    </tr>
                    <tr>
                        <td class="layoutWithBgColor" nowrap="nowrap">current project:</td>
                        <td class="layoutWithBgColor" nowrap="nowrap">
                            <a class="noStyle" title="{$projectTreeDyn->getPathAsString($currentTask['projectTree_id'])}">
                                {$projectTreeDyn->getPathAsString($currentTask['projectTree_id'],50)}
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td class="layoutWithBgColor" nowrap="nowrap">current task:</td>
                        <td class="layoutWithBgColor" nowrap="nowrap">
                            {$currentTask['_task_name']}
                            - since&nbsp;{$dateTime->formatTimeShort($currentTask['timestamp'])}
                        </td>
                    </tr>
                </table>
            </td>

        <td class="layoutWithBgColor" align="right">
            <a href="{$config->logourl}" target="_blank">
            <img src="tt_logo.gif" width="129" height="50" alt="Logo"></a>
        </td>
    </tr>

<!-- top statusleiste  -->   
    <tr>
        <td colspan="3" class="layoutWithBgColor" align="center">
            <div class="table statusLine head">
                <div class="tr">
                    {if( $userAuth->isLoggedIn() )}
                        <div class="td">
                            <b>User</b>
                        </div>
                        <div class="td">
                            <form action="{$config->applPathPrefix}/modules/user/login.php" method="post">
                                <input type="hidden" name="logout" value="1">
                                <input type="submit" value="logout" class="button">
                            </form>
                        </div>

                    {if( sizeof($noneProjectTasks) )}
                        <div class="td">
                            <b>Hot-Keys</b>
                        </div>
                        <div class="td">
                            <form name="shortForm" action="{$config->applPathPrefix}/modules/time/shortcut.php" method="post" onsubmit="return confirmShort();">
                                <input type="hidden" name="shortoverBooked" id="shortoverBooked" value="0">
                                <input type="hidden" name="shortrestAvailable" id="shortrestAvailable" value="0">
                                <input type="hidden" name="currTask" id="currTask" value="0">
                                <input type="hidden" name="projectTree_id" id="projectTree_id" value="{$currentTask['projectTree_id']}">
                                {foreach( $noneProjectTasks as $aNoneTask)}
                                    <input name="shortcutTaskId[{$aNoneTask['id']}]" type="submit" 
                                           value="{$aNoneTask['name']}" class="button" onclick="javascript:document.shortForm.currTask.value={$aNoneTask['id']}">
                            </form>
                        </div>

                    {if($userAuth->isLoggedIn() && sizeof($today) > 0)}
                        <div class="td">
                            <b>Info</b>
                        </div>

                        <div class="td" style="width:100%;">
                            <div class="table layoutWithBgColor">
                                <div class="tr">
                                    <div class="td layoutWithBgColor">
                                        <a href="{$config->applPathPrefix}/modules/time/today.php">today</a>&nbsp;
                                    </div>
                                    <div class="td" style="white-space:nowrap;">
                                        {$dateTime->formatTimeShort($today[0]['timestamp'])}&nbsp;
                                        {foreach($today as $aTask)}
                                            <a title="{$aTask['_title']}">
                                                <span style="background-color:{$aTask['_task_color']}; border:0px;">
                                                    <img src="pixel.gif" width="{$time->getImgWidth($aTask)}" height="10" alt="">
                                                </span>
                                            </a>
                                        &nbsp;{$dateTime->formatTimeShort($aTask['timestamp'])}
                                    </div>
                                </div>
                            </div>
                        </div>
                    {else}
                        <div class="td" style="width:100%;">
                            &nbsp;
                        </div>
                </div>
            </div>
        </td>
    </tr>
</table>

<script>
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

	bookDate = day+'.'+month+'.'+year;
	bookTime = hours + ":" + minutes;
	taskId = document.shortForm.currTask.value;
	projectTreeId = document.shortForm.projectTree_id.value;
	oldid = 0;

	xajax.call( 'checkBookings', \{ mode:'synchronous', parameters:[projectTreeId,taskId,oldid,bookDate,bookTime,'short'] \} );

	if (document.shortForm["shortoverBooked"].value && document.shortForm["shortoverBooked"].value != "0") \{
            neg = document.shortForm["shortrestAvailable"].value.indexOf('-');
            if (neg != -1) \{
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
