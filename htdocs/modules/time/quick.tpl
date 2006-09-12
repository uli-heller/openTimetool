<!--
    $Log: quick.tpl,v $
    Revision 1.19.2.2  2003/03/28 10:18:20  wk
    - show the complete project name on mouseover, to make the full name available too

    Revision 1.19.2.1  2003/03/28 10:06:59  wk
    - truncate the project name when it gets too long

    Revision 1.19  2003/02/18 20:14:01  wk
    - layouting

    Revision 1.18  2003/02/11 11:43:33  wk
    - add class button where it was missing

    Revision 1.17  2003/02/10 19:27:24  wk
    - use projectTreeDyn now

    Revision 1.16  2003/02/10 16:14:52  wk
    - use new tree view
    - use button class (for IE)

    Revision 1.15  2002/11/29 14:52:33  jv
    - change order of info-button and text  -

    Revision 1.14  2002/11/22 20:13:09  wk
    - added all help-buttons properly

    Revision 1.13  2002/10/24 14:14:16  wk
    - some renaming of buttons, etc.

    Revision 1.12  2002/10/22 14:28:32  wk
    - use new macro names

    Revision 1.11  2002/09/23 09:34:59  wk
    - typo

    Revision 1.10  2002/09/12 08:18:33  wk
    - outline the text better

    Revision 1.9  2002/09/12 08:16:30  wk
    - outline the text better

    Revision 1.8  2002/09/11 15:50:13  wk
    - IE needs the included js-files before calling any of the methods

    Revision 1.7  2002/08/29 13:26:10  wk
    - use macros and common.js
    - show the comment in the overview too

    Revision 1.6  2002/08/21 20:22:30  wk
    - dont use deprecated macro anymore

    Revision 1.5  2002/08/20 16:28:37  wk
    - use macros for date picker

    Revision 1.4  2002/08/14 16:17:55  wk
    - use calendar and some smaller changes

    Revision 1.3  2002/07/25 11:58:12  wk
    - bugfix prevent use of edit-mode

    Revision 1.2  2002/07/24 17:09:16  wk
    - use tree
    - show now button and update date automagically

    Revision 1.1.1.1  2002/07/22 09:37:37  wk


-->

{%include vp/Application/HTML/Macro/Tree.mcr%}
{%include common/macro/task.mcr%}
{%include common/macro/project.mcr%}
{%include common/macro/time.mcr%}
{%include common/macro/common.mcr%}
{%include common/macro/table.mcr%}

<center>

<table width="100%" class="outline">
    <tr>
        <th colspan="5">
            {%common_help()%}&nbsp;Quick-Log
        </th>
    </tr>

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
                <form method="post" action="{$_SERVER['PHP_SELF']}">
                    <input type="hidden" class="button" name="quickLog" value="{$aTime['id']}">
                    <input type="submit" value="now!" class="button">
                </form>
                <!--<a href="{$_SERVER['PHP_SELF']}?quickLog={$aTime['id']}">Log now</a>-->
            </td>
        </tr>
</table>

<br><br>

<form method="post" name="editForm" action="{$_SERVER['PHP_SELF']}">
    <input type="hidden" name="newData[user_id]" value="{$data['user_id']}">
    <table width="100%" class="outline">
        {%table_headline( 'Logging' , 'log' )%}
        <tr>
            <td>time</td>
            <td>
                {%common_dateInput( 'newData[timestamp_date]' , $data['timestamp'] , 'editForm' )%}
                <input name="newData[timestamp_time]" value="{echo date('H:i',$data['timestamp'])}" size="5" onBlur="autoCorrectTime('editForm','newData[timestamp_time]')">
                <input type="button" class="button" value="refresh" onClick="_updateTime()">
            </td>
        </tr>

        {%project_row($lastTime['projectTree_id'])%}
        {%task_row($tasks,$lastTime['task_id'])%}
        {%common_commentRow($lastTime['comment'])%}

        <tr>
            <td colspan="2" align="center">
                <input type="submit" class="button" name="action_saveAsNew" value="save">
            </td>
        </tr>
    </table>
</form>
</center>

{%common_getJS('common')%}
{%common_getJS('calendar')%}
{%common_getJS($projectTreeJsFile,true)%}
<script type="text/javascript" language="JavaScript">

    lastDate = document.editForm["newData[timestamp_date]"].value;
    lastTime = document.editForm["newData[timestamp_time]"].value;

    updateTime();

    projectTree.init(true);
</script>
