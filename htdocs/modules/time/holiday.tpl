<!--

$Id$

-->

{%include common/macro/task.mcr%}
{%include common/macro/project.mcr%}
{%include common/macro/common.mcr%}
{%include common/macro/user.mcr%}
{%include common/macro/table.mcr%}
{%include vp/Application/HTML/Macro/EditData.mcr%}
{%include vp/Application/HTML/Macro/Tree.mcr%}

Use this page to log times for a longer period (i.e. 2 weeks for your holidays, etc.).
<br><br>

<form method="post" name="editForm" onsubmit="return confirmSave()">
    <input type="hidden" name="overBooked" id="overBooked" value="0">
    <input type="hidden" name="restAvailable" id="restAvailable" value="0">
    <table class="outline">
        <thead>
            {%table_headline('Period-Log')%}
        </thead>

        <tfoot>
            <tr>
                <td nowrap="nowrap">&nbsp;</td>
                <td nowrap="nowrap">
                    <input type="submit" name="action_save" value="Save" class="button">
                </td>
            </tr>
        </tfoot>

        <tbody>
            {if( $isAdmin )}
                <tr>
                    <td>User</td>
                    <td>
                        <select name="user_id">
                            {%user_asOptions( $users , $userId )%}
                        </select>
                    </td>
                </tr>
            {else}
                <input type="hidden" name="user_id" value="{$userId}">

            <tr>
                <td>start task</td>
                <td>
                    {%common_timeInput( 'newData[startTime]' , $data['startTime'] )%}
                    <select name="newData[startTask_id]" style="width:200px;">
                        {%tasksAsOptions($tasks,$data['startTask_id'])%}
                    </select>
                </td>
            </tr>

            <tr>
                <td>end task</td>
                <td>
                    {%common_timeInput( 'newData[endTime]' , $data['endTime'] )%}
                    <select name="newData[endTask_id]" style="width:200px;">
                        {%task_asOptions($endTasks,$data['endTask_id'])%}
                    </select>
                </td>
            </tr>

            <tr>
                <td>from</td>
                <td>
                    {%common_dateInput( 'newData[startDate]' , $data['startDate'] )%}
                </td>
            <tr>

            <tr>
                <td>until</td>
                <td>
                    {%common_dateInput( 'newData[endDate]' , $data['endDate'] )%}
                </td>
            <tr>

            <!--  AK : added @ -->
            {%project_row(@$data['projectTree_id'])%}  
            {%common_commentRow(@$data['comment'])%}	 

        </tbody>
    </table>
</form>

<div id="msgxajax" name="msgxajax"></div>

<script>
    function confirmSave()
    \{
        //project = document.editForm["newData[projectTree_id]"][document.editForm["newData[projectTree_id]"].selectedIndex].text.replace(/^(\s*-)*\s*/,"");
        project = projectTree.getPathAsString(document.editForm["newData[projectTree_id]"].value);
        projectTree_id = document.editForm["newData[projectTree_id]"].value;
        if (!project) \{
            project = projectTree.getPathAsString(document.editForm["lastProjectId"].value);
            projectTree_id = document.editForm["lastProjectId"].value;
        \}
        if (!project) \{
            alert("Please select a project!");
            return false;
        \}
        startTask = document.editForm["newData[startTask_id]"][document.editForm["newData[startTask_id]"].selectedIndex].text;
        endTask = document.editForm["newData[endTask_id]"][document.editForm["newData[endTask_id]"].selectedIndex].text;
        startTaskc = document.editForm["newData[startTask_id]"][document.editForm["newData[startTask_id]"].selectedIndex].value;
        endTaskc = document.editForm["newData[endTask_id]"][document.editForm["newData[endTask_id]"].selectedIndex].value;
        startDate = document.editForm["newData[startDate]"].value;
        endDate = document.editForm["newData[endDate]"].value;
        startTime = document.editForm["newData[startTime]"].value;
        endTime = document.editForm["newData[endTime]"].value;

        // first check overbooking
	xajax.call( 'checkperiodBookings', \{ mode:'synchronous', parameters:[projectTree_id,startDate,endDate,startTime,endTime,startTaskc,endTaskc] \} );

	//return false;
	if (document.editForm["overBooked"].value && document.editForm["overBooked"].value != "0") \{
            neg = document.editForm["restAvailable"].value.indexOf('-');
            if (neg != -1) \{
                overbooked = document.editForm["restAvailable"].value.substr(1);
                message = "{$T_MSG_PROJECT_OVERBOOKED}"+"\n{$T_MSG_PROJECT_BOOKING_CHOICE_QUESTION}\n\n{$T_MSG_PROJECT_BOOKING_CHOICE_CANCEL}\n{$T_MSG_PROJECT_BOOKING_CHOICE_OK}\n";
            \} else \{
                message = "{$T_MSG_PROJECT_OVERBOOKED21}"+document.editForm["restAvailable"].value+" "+"{$T_MSG_PROJECT_OVERBOOKED22}\n{$T_MSG_PROJECT_BOOKING_CHOICE_QUESTION}\n\n{$T_MSG_PROJECT_BOOKING_CHOICE_CANCEL}\n{$T_MSG_PROJECT_BOOKING_CHOICE_OK}\n";
            \}
            return confirm(message);	  
	\} 

	// now the are you sure-alert as before
        time = startTime+" ("+startTask+") - "+endTime+" ("+endTask+")";

        message = "{$T_MSG_LOG_FOR_PROJECT} '"+project+"' \r\n"+
                  time + "\r\n{$T_MSG_FOR} " + startDate + " - " + endDate +
                  "\r\n\r\n{$T_MSG_ARE_YOUR_SURE}";

        return confirm(message);
    \}
</script>

{%common_getJS('calendar')%}
{%common_getJS('common')%}
{%common_getJS($projectTreeJsFile,true)%}

<script>
    projectTree.init(true);
</script>
