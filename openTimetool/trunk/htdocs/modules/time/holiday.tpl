<!--
    $Id
    Revision 1.16  2006/08/29 20:34:44  AK
    - eliminate php notices
    
    $Log: holiday.tpl,v $
    Revision 1.15  2003/02/11 12:34:44  wk
    - use proper JS-file depending on isAdmin

    Revision 1.14  2003/02/10 19:26:45  wk
    - make the JS check work again

    Revision 1.13  2003/02/10 19:14:33  wk
    - use projectTreeDyn now

    Revision 1.12  2003/02/10 16:10:00  wk
    - use new projectTree
    - set width of table

    Revision 1.11  2002/12/02 15:25:54  wk
    - translate JS-text

    Revision 1.10  2002/11/30 13:06:41  wk
    - changed text a bit

    Revision 1.9  2002/11/29 17:03:43  jv
    - change info on top again -

    Revision 1.8  2002/11/29 16:49:40  jv
    - change info on top -

    Revision 1.7  2002/11/29 16:47:30  jv
    - add info on top -

    Revision 1.6  2002/11/22 20:13:09  wk
    - added all help-buttons properly

    Revision 1.5  2002/11/19 20:00:48  wk
    - renamed the page and removed text

    Revision 1.4  2002/10/31 17:47:14  wk
    - added help icons

    Revision 1.3  2002/10/22 14:26:23  wk
    - use new macro names

    Revision 1.2  2002/09/11 19:20:52  wk
    - show valid endtasks only

    Revision 1.1  2002/08/29 13:25:28  wk
    - intial checkin

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

<form method="post" name="editForm" onSubmit="return confirmSave()">
    <table class="outline">
        {%table_headline('Period-Log')%}


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

		<!-- preselect the last used project for selection menu below  --> 
        { $data['projectTree_id'] = $lastProject }
        
        <tr>
            <td>start task</td>
            <td width="100%">
                {%common_timeInput( 'newData[startTime]' , $data['startTime'] )%}
                <select name="newData[startTask_id]">
                    {%tasksAsOptions($tasks,$data['startTask_id'])%}
                </select>
            </td>
        </tr>

        <tr>
            <td>end task</td>
            <td>
                {%common_timeInput( 'newData[endTime]' , $data['endTime'] )%}
                <select name="newData[endTask_id]">
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

        <tr>
            <td nowrap="nowrap">&nbsp;</td>
            <td nowrap="nowrap">
                <input type="submit" name="action_save" value="Save" class="button">
            </td>
        </tr>

    </table>
</form>

<script type="text/javascript" language="JavaScript">
    function confirmSave()
    \{
        //project = document.editForm["newData[projectTree_id]"][document.editForm["newData[projectTree_id]"].selectedIndex].text.replace(/^(\s*-)*\s*/,"");;
        project = projectTree.getPathAsString(document.editForm["newData[projectTree_id]"].value);
        if (!project) \{
            project = projectTree.getPathAsString(document.editForm["lastProjectId"].value);            
        \}
        if (!project) \{
            alert("Please select a project!");
            return false;
        \}
        startTask = document.editForm["newData[startTask_id]"][document.editForm["newData[startTask_id]"].selectedIndex].text;
        endTask = document.editForm["newData[endTask_id]"][document.editForm["newData[endTask_id]"].selectedIndex].text;
        startDate = document.editForm["newData[startDate]"].value;
        endDate = document.editForm["newData[endDate]"].value;
        startTime = document.editForm["newData[startTime]"].value;
        endTime = document.editForm["newData[endTime]"].value;

        time = startTime+" ("+startTask+") - "+endTime+" ("+endTask+")";

        message =   "{$T_MSG_LOG_FOR_PROJECT} '"+project+"' \r\n"+
                    time + "\r\n{$T_MSG_FOR} " + startDate + " - " + endDate +
                    "\r\n\r\n{$T_MSG_ARE_YOUR_SURE}";


        return confirm(message)
    \}
</script>
{%common_getJS('calendar')%}
{%common_getJS('common')%}
{%common_getJS($projectTreeJsFile,true)%}

<script type="text/javascript" language="JavaScript">
    projectTree.init(true);
</script>
