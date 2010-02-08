<!--
    $Id
    Revision 1.9  2006/08/30 20:57:02  AK
    - eliminate various php notices
    
    $Log: multi.tpl,v $
    Revision 1.8  2003/01/28 10:57:02  wk
    - bugfix for auto-set-date
    - add headline for quick-code

    Revision 1.7  2002/11/22 20:13:09  wk
    - added all help-buttons properly

    Revision 1.6  2002/11/13 19:01:46  wk
    - some admin handling

    Revision 1.5  2002/10/29 10:34:29  wk
    - enhance the project-task input to use the project and task names as input

    Revision 1.4  2002/10/24 18:43:41  wk
    - unify the texts

    Revision 1.3  2002/10/24 14:14:01  wk
    - relayout

    Revision 1.2  2002/10/22 14:27:28  wk
    - set the values if any given, in case of error

    Revision 1.1  2002/10/21 18:26:56  wk
    - initial commit

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


<form method="post" name="editForm" action="{$_SERVER['PHP_SELF']}">
    <table class="outline">

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
<!-- AK added @ to all $data occurences; I think we need that for the initial state -->
        {%repeat 5 times $x%}
            <tr>
                <td valign="top">
                    {%common_dateInput( "newData[$x][timestamp_date]" , @$data[$x]['timestamp_date'] , 'editForm' , '' , true )%}
                </td>
                <td valign="top">
                    <input name="newData[{$x}][timestamp_time]" size="5" value="{@$data[$x]['timestamp_time']}"
                            onFocus="fillDateIfEmpty({$x})"
                            onBlur="autoCorrectTime('editForm','newData[{$x}][timestamp_time]');">
                </td>
                <td align="center" valign="top">
                    <input size="10" name="newData[{$x}][projectAndTask]" value="{@$data[$x]['projectAndTask']}"
                            onFocus="fillDateIfEmpty({$x})"
                            onBlur="handleProjectTaskInput({$x},true)" onKeyDown="handleProjectTaskInput({$x})" onKeyUp="handleProjectTaskInput({$x})">
                </td>
                <td valign="top">
                    <select name="newData[{$x}][projectTree_id]" onFocus="autoFocus()" onChange="updateProjectAndTask({$x})" onBlur="updateProjectAndTask({$x})">
                        {%Tree_asOptions($allFolders,@$data[$x]['projectTree_id'])%}
                    </select>
                </td>
                <td valign="top">
                    <select name="newData[{$x}][task_id]" onFocus="autoFocus()" onChange="updateProjectAndTask({$x})" onBlur="updateProjectAndTask({$x})">
                        {%task_asOptions($tasks,@$data[$x]['task_id'])%}
                    </select>
                </td>
                <td valign="top">
                    <textarea name="newData[{$x}][comment]" rows="2" cols="30" onFocus="autoFocus()">{@$data[$x]['comment']}</textarea>
                </td>
            </tr>

        <tr>
            <td colspan="6" align="center">
                <input type="submit" name="action_saveAsNew" value="save">
            </td>
        </tr>
    </table>
</form>
                     


{%common_getJS('calendar')%}
<script type="text/javascript" language="JavaScript">

                   
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
            // we have to use a little timeout here, because the onFocus event 
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