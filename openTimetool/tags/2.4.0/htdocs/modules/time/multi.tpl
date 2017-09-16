<!--

$Id$

-->

{%include vp/Application/HTML/Macro/Tree.mcr%}
{%include vp/Application/HTML/Macro/Form.mcr%}
{%include common/macro/task.mcr%}
{%include common/macro/time.mcr%}
{%include common/macro/common.mcr%}
{%include common/macro/user.mcr%}

Use this page to log multiple times.
It uses the {%common_help('autoCorrect','auto correct','features')%} functionality for helping you to fill in the fields.

<br><br>

<form method="post" name="editForm" onsubmit="return confirmSave()">
    <input type="hidden" name="overBooked" id="overBooked" value="0">
    <input type="hidden" name="restAvailable" id="restAvailable" value="0">
    <table class="outline">
        <thead>
            {if( $isAdmin )}
                <tr>
                    <td>User</td>
                    <td colspan="5">
                        <select name="user_id">
                            {%user_asOptions( $users , $userId )%}
                        </select>
                    </td>
                </tr>
            {else}
                <input type="hidden" name="user_id" value="{$userId}">

            <tr>
                <th>date</th>
                <th>time</th>
                <th>quick code</th>
                <th>project</th>
                <th>task</th>
                <th>comment</th>
            </tr>
        </thead>

        <tfoot>
            <tr>
                <td colspan="6" align="center">
                    <input type="submit" name="action_saveAsNew" value="save">
                </td>
            </tr>
        </tfoot>

        <tbody>
        <!-- AK added @ to all $data occurences; I think we need that for the initial state -->
        {%repeat 5 times $x%}
            <tr>
                <td valign="top">
                    {%common_dateInput( "newData[$x][timestamp_date]" , @$data[$x]['timestamp_date'] , 'editForm' , '' , true )%}
                </td>
                <td valign="top">
                    <input name="newData[{$x}][timestamp_time]" size="5" value="{@$data[$x]['timestamp_time']}"
                            onfocus="fillDateIfEmpty({$x})"
                            onblur="autoCorrectTime('editForm','newData[{$x}][timestamp_time]');">
                </td>
                <td align="center" valign="top">
                    <input size="10" name="newData[{$x}][projectAndTask]" value="{@$data[$x]['projectAndTask']}"
                            onfocus="fillDateIfEmpty({$x})"
                            onblur="handleProjectTaskInput({$x},true)" onkeydown="handleProjectTaskInput({$x})" onkeyup="handleProjectTaskInput({$x})">
                </td>
                <td valign="top">
                    <select name="newData[{$x}][projectTree_id]" onfocus="autoFocus()" onchange="updateProjectAndTask({$x})" onblur="updateProjectAndTask({$x})" style="max-width:400px;">
                        {%Tree_asOptions($allFolders,@$data[$x]['projectTree_id'])%}
                    </select>
                </td>
                <td valign="top">
                    <select name="newData[{$x}][task_id]" onfocus="autoFocus()" onchange="updateProjectAndTask({$x})" onblur="updateProjectAndTask({$x})" style="max-width:200px;">
                        {%task_asOptions($tasks,@$data[$x]['task_id'])%}
                    </select>
                </td>
                <td valign="top">
                    <textarea name="newData[{$x}][comment]" rows="2" cols="30" onfocus="autoFocus()">{@$data[$x]['comment']}</textarea>
                </td>
            </tr>
        </tbody>
    </table>
</form>

<div id="msgxajax" name="msgxajax"></div>

<script>
    function confirmSave()
    \{
        // we have to run through all 5 bookins and call our check. If one fails, we  
        var bookDate = new Array();
        var bookTime = new Array();
        var taskId = new Array();
        var projectTreeId = new Array();
        var i,ix;
        for(i=0;i<5;i++) \{
            ix = "newData["+i+"][timestamp_date]";
            bookDate[i] = document.editForm[ix].value;
        \}
        for(i=0;i<5;i++) \{
            ix = "newData["+i+"][timestamp_time]";
            bookTime[i] = document.editForm[ix].value;
        \}
        for(i=0;i<5;i++) \{
            ix = "newData["+i+"][task_id]";
            taskId[i] = document.editForm[ix].value;
        \}
        for(i=0;i<5;i++) \{
            ix = "newData["+i+"][projectTree_id]";
            projectTreeId[i] = document.editForm[ix].value;
        \}
        oldid = 0;

        xajax.call( 'checkmultipleBookings', \{ mode:'synchronous', parameters:[projectTreeId,taskId,oldid,bookDate,bookTime] \} );

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
        \} else \{
            return true;
        \}
    \}
</script>

{%common_getJS('calendar')%}

<script>
    var tasks = new Array();
    { $i=0}
    {foreach( $tasks as $aTask )}
        tasks[{$i++}] = new Array("{$aTask['name']}","{$aTask['id']}");

    var projects = new Array();
    { $i=0}
    {foreach( $allFolders as $aProj )}
        projects[{$i++}] = new Array("{$aProj['name']}","{$aProj['id']}");

    var focusOn = null;

    /**
    *   update project and task drop downs dynamically
    *   if a id is given
    *
    *   @param  int     the index of the row that is currently edited
    *   @param  boolean set to true if the input field will be left, this is used
    *                   to determine which field to focus next
    *                   if a project and task is given focus on the next line
    */
    function handleProjectTaskInput( index , blured )
    \{
        curValue = document.editForm["newData["+index+"][projectAndTask]"].value;
        taskName = "";
        projectName = "";
        if( curValue.search(/ /) != -1 )
        \{       
            projectId = parseInt(curValue.split(' ')[0]);
            if( !projectId )
                projectName = curValue.split(' ')[0];
            taskId = parseInt(curValue.split(' ')[1]);
            if( !taskId )
                taskName = curValue.split(' ')[1];
        \}
        else
        \{       
            projectId = parseInt(curValue);
            if( !projectId )
                projectName = curValue;
            taskId = null;
        \}

        if( projectName.length )
        \{
            for( i=0 ; i<projects.length ; i++ )
            \{
                if( projects[i][0].substr(0,projectName.length).toLowerCase() == projectName.toLowerCase() )
                \{
                    projectId = projects[i][1];
                    break;
                \}
            \}
        \}

        _projects = document.editForm["newData["+index+"][projectTree_id]"].options;
        for( i=0 ; i<_projects.length ; i++ )
        \{
            if( _projects[i].value == projectId )
            \{
                document.editForm["newData["+index+"][projectTree_id]"].selectedIndex = i;
                break;
            \}
        \}

        if( taskName.length )
        \{
            for( i=0 ; i<tasks.length ; i++ )
            \{
                if( tasks[i][0].substr(0,taskName.length).toLowerCase() == taskName.toLowerCase() )
                \{
                    taskId = tasks[i][1];
                    break;
                \}
            \}
        \}

        if( taskId )
        \{
            _tasks = document.editForm["newData["+index+"][task_id]"].options;
            for( i=0 ; i<_tasks.length ; i++ )
            \{
                if( _tasks[i].value == taskId )
                \{
                    document.editForm["newData["+index+"][task_id]"].selectedIndex = i;
                    break;
                \}
            \}
        \}

        // if the input field shall be left, try to go to the next one
        // that needs to be filled, i.e. if project#task is given go to the next row
        // if only project is given go to the task input on this row
        if( blured == true )
        \{
            if( taskId )
            \{
                focusOn = "newData["+index+"][comment]";
            \}
            else
            \{
                focusOn = "newData["+index+"][task_id]";
            \}
        \}          

    \}

    /**
    *   autofocus on the element set by the function above
    *   do that only once, so the user can also edit this field
    *   and the focus will not always be removed on focusing this element
    */
    function autoFocus()
    \{
        if( focusOn != null )
        \{               
            // we have to use a little timeout here, because the onfocus event 
            // seems to get called to fast in a row when it appears, and the focusOn doesnt 
            // get set to null, so we do it with some timeout
            window.setTimeout("document.editForm['"+focusOn+"'].focus()",100);
        \}
        focusOn = null;
    \}

    /**
    *   update project and task fields when changing the values in the drop downs
    */
    function updateProjectAndTask( index )
    \{
/*        project = document.editForm["newData["+index+"][projectTree_id]"];
        s = project[project.selectedIndex].text;

        task = document.editForm["newData["+index+"][task_id]"];
        s += " "+task[task.selectedIndex].text;
*/
        s = "";
        document.editForm["newData["+index+"][projectAndTask]"].value = s;
    \}

    function fillDateIfEmpty( x )
    \{                                 
        if( !document.editForm["newData["+x+"][timestamp_date]"].value && x>0 )
            document.editForm["newData["+x+"][timestamp_date]"].value = document.editForm["newData["+(x-1)+"][timestamp_date]"].value
              
        // do only set the value of the date, if it is empty :-)
        if( x == 0 && !document.editForm["newData["+x+"][timestamp_date]"].value )
        \{
            month = new Date().getMonth()+1;
            year = new Date().getFullYear();
            day = new Date().getDate();
            document.editForm["newData["+x+"][timestamp_date]"].value = day+"."+month+"."+year;
        \}
    \}
</script>
