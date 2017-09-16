<!--

$Id$

-->

{%include vp/Application/HTML/Macro/Tree.mcr%}
{%include common/macro/task.mcr%}
{%include common/macro/project.mcr%}
{%include common/macro/time.mcr%}
{%include common/macro/common.mcr%}
{%include common/macro/table.mcr%}

<table width="100%" class="outline">
    <thead>
        <tr>
            <th colspan="5">
                {%common_help()%}&nbsp;Quick-Log
            </th>
        </tr>
    </thead>

    <tbody>
    {foreach( $lastTimes as $aTime )}
        <tr>
            <td valign="top">
                <a class="noStyle" title="{$projectTreeDyn->getPathAsString($aTime['projectTree_id'])}">
                    {$projectTreeDyn->getPathAsString($aTime['projectTree_id'],40)}
                </a>
            </td>
            <td valign="top">{$aTime['_task_name']}</td>
            <td valign="top">{%trim $aTime['comment'] 30 "..."%}</td>
            <td>
                <form method="post" onsubmit="return confirmQuickSave({$aTime['projectTree_id']},{$aTime['task_id']});">
                    <input type="hidden" name="quickLog" value="{$aTime['id']}">
                    <input type="submit" value="now!" class="button">
                </form>
                <!--<a href="{$_SERVER['PHP_SELF']}?quickLog={$aTime['id']}">Log now</a>-->
            </td>
        </tr>
    </tbody>
</table>

<br><br>

<form method="post" name="editForm" onsubmit="return confirmSave();">
    <input type="hidden" name="newData[user_id]" value="{$data['user_id']}">
    <input type="hidden" name="overBooked" id="overBooked" value="0">
    <input type="hidden" name="restAvailable" id="restAvailable" value="0">
    <table width="100%" class="outline">
        <thead>
            {%table_headline( 'Logging' , 'log' )%}
        </thead>

        <tfoot>
            <tr>
                <td colspan="2" align="center">
                    <input type="submit" class="button" name="action_saveAsNew" value="save">
                </td>
            </tr>
        </tfoot>

        <tbody>
            <tr>
                <td>time</td>
                <td>
                    {%common_dateInput( 'newData[timestamp_date]' , $data['timestamp'] , 'editForm' )%}
                    <input name="newData[timestamp_time]" value="{echo date('H:i',$data['timestamp'])}" size="5" onblur="autoCorrectTime('editForm','newData[timestamp_time]')">
                    <input type="button" class="button" value="refresh" onclick="_updateTime()">
                </td>
            </tr>

            {%project_row($lastTime['projectTree_id'])%}
            {%task_row($tasks,$lastTime['task_id'])%}
            {%common_commentRow($lastTime['comment'])%}

        </tbody>
    </table>
</form>

{%common_getJS('common')%}
{%common_getJS('calendar')%}
{%common_getJS($projectTreeJsFile,true)%}

<script>
    lastDate = document.editForm["newData[timestamp_date]"].value;
    lastTime = document.editForm["newData[timestamp_time]"].value;

    updateTime();

    projectTree.init(true);
</script>

<div id="msgxajax" name="msgxajax"></div>

<script>
    function confirmSave()
    \{
        bookDate = document.editForm["newData[timestamp_date]"].value;
        bookTime = document.editForm["newData[timestamp_time]"].value;
        taskId = document.editForm["newData[task_id]"].value;
        projectTreeId = document.editForm["newData[projectTree_id]"].value;
        oldid = 0;

        xajax.call( 'checkBookings', \{ mode:'synchronous', parameters:[projectTreeId,taskId,oldid,bookDate,bookTime] \} );

        if (document.editForm["overBooked"].value && document.editForm["overBooked"].value != "0") \{
            neg = document.editForm["restAvailable"].value.indexOf('-');
            if (neg != -1) \{
                overbooked = document.editForm["restAvailable"].value.substr(1);
                message = "{$T_MSG_PROJECT_OVERBOOKED}"+"\n{$T_MSG_PROJECT_BOOKING_CHOICE_QUESTION}\n\n{$T_MSG_PROJECT_BOOKING_CHOICE_CANCEL}\n{$T_MSG_PROJECT_BOOKING_CHOICE_OK}\n";
            \} else \{
                message = "{$T_MSG_PROJECT_OVERBOOKED21}"+document.editForm["restAvailable"].value+" "+"{$T_MSG_PROJECT_OVERBOOKED22}\n{$T_MSG_PROJECT_BOOKING_CHOICE_QUESTION}\n\n{$T_MSG_PROJECT_BOOKING_CHOICE_CANCEL}\n{$T_MSG_PROJECT_BOOKING_CHOICE_OK}\n";
            \}
            return confirm(message);	  
        \} else \{
            return true;
        \}
    \}

    function confirmQuickSave(projectTreeId,taskId)
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

        oldid = 0;

        xajax.call( 'checkBookings', \{ mode:'synchronous', parameters:[projectTreeId,taskId,oldid,bookDate,bookTime] \} );

        if (document.editForm["overBooked"].value && document.editForm["overBooked"].value != "0") \{
            neg = document.editForm["restAvailable"].value.indexOf('-');
            if (neg != -1) \{
                overbooked = document.editForm["restAvailable"].value.substr(1);
                message = "{$T_MSG_PROJECT_OVERBOOKED}"+"\n{$T_MSG_PROJECT_BOOKING_CHOICE_QUESTION}\n\n{$T_MSG_PROJECT_BOOKING_CHOICE_CANCEL}\n{$T_MSG_PROJECT_BOOKING_CHOICE_OK}\n";
            \} else \{
                message = "{$T_MSG_PROJECT_OVERBOOKED21}"+document.editForm["restAvailable"].value+" "+"{$T_MSG_PROJECT_OVERBOOKED22}\n{$T_MSG_PROJECT_BOOKING_CHOICE_QUESTION}\n\n{$T_MSG_PROJECT_BOOKING_CHOICE_CANCEL}\n{$T_MSG_PROJECT_BOOKING_CHOICE_OK}\n";
            \}
            return confirm(message);	  
        \} else \{
            return true;
        \}
    \}
</script>
