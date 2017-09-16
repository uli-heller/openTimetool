<!--

$Id$

-->

{%include vp/Application/HTML/Macro/Tree.mcr%}
{%include common/macro/task.mcr%}
{%include common/macro/project.mcr%}
{%include common/macro/time.mcr%}
{%include common/macro/user.mcr%}
{%include common/macro/common.mcr%}
{%include common/macro/table.mcr%}

<!--
<div id="editWinDiv" style="position:absolute; visibility:hidden;">
-->
{if ($doEdit)}
    <form method="post" name="editForm" action="{$_SERVER['PHP_SELF']}">
        <input type="hidden" name="newData[id]" value="{$data['id']}">
        <table class="outline">
            <thead>
                {%table_headline('Edit/Add entry')%}
            </thead>

            <tfoot>
                <tr>
                    <td>&nbsp;</td>
                    <td>
                        {if($data['id'])}
                            <input type="submit" name="action_save" value="Update" class="button">
                            <input type="submit" name="action_saveAsNew" value="Save as new" class="button">
                        {else}
                            <input type="submit" name="action_saveAsNew" value="Save" class="button">
                        <input type="button" value="Cancel" onclick="window.location='{$_SERVER['PHP_SELF']}'" class="button">
                    </td>
                </tr>
            </tfoot>

            <tbody>
                <tr>
                    <td>time</td>
                    <td width="100%">
                        {%common_dateInput( 'newData[timestamp_date]' , $data['timestamp'] , 'editForm' )%}
                        {%common_timeInput( 'newData[timestamp_time]' , $data['timestamp'] )%}
                        <input type="button" value="now!" onclick="_updateTime()" class="button">
                    </td>
                </tr>

                {%project_row($data['projectTree_id'])%}
                {%task_row($tasks,@$data['task_id'])%}
                {%common_commentRow(@$data['comment'])%}

            </tbody>
        </table>
    </form>
<!--
</div>
-->
<table>
    <thead>
        <tr>
            <th colspan="2">
                Overview from 
                {$dateTime->formatDate($fromDay)} through {$dateTime->formatDate($untilDay)}
<!--
                <input type="button" class="button" value="&laquo;">
                <input type="button" class="button" value="&raquo;">
-->
            </th>
        </tr>
    </thead>

    <tbody>
    {foreach($times as $aTimes)}    
        <tr>
            <td>&nbsp;</td>
            <td>
                <table class="outline">
                    <tr>
                    {for($i=0;$i<24;$i++)}
                        <td style="padding:0px; text-align:center; width:{$oneHourWidth-2}px">
                            {$i}
                        </td>
                </table>
            </td>
        </tr>

        <tr>
            <td nowrap="nowrap">
                {$dateTime->formatDate($aTimes[1]['timestamp'],$myFormat)}
            </td>
            <td valign="middle">
            {foreach($aTimes as $aTime)}
                {if( isset($aTime['_endOfDay']))}
                    &nbsp;
                    <a href="{$_SERVER['PHP_SELF']}?id={$aTime['id']}" title="{$aTime['_title']}" onclick="showEditWin({$aTime['id']})"><span style="background-color:{$aTime['_task_color']}; border:0; padding:0; margin:0;">
                        {$aTime['_task_name']}</span></a>
                {else}
                    <!--
                    <a href="javascript://" title="{$aTime['_title']}" onclick="showEditWin({$aTime['id']})"><span style="background-color:{$aTime['_task_color']}; border:0; padding:0; margin:0;">
                    -->
                    <a href="{$_SERVER['PHP_SELF']}?id={$aTime['id']}" title="{$aTime['_title']}"><span style="background-color:{$aTime['_task_color']}; border:0; padding:0; margin:0;">
                        <img src="pixel.gif" width="{$time->getImgWidth($aTime,3)}" height="10" alt=""></span></a>
            </td>
        </tr>
    </tbody>
</table>

<br><br>

<table class="outline">
    <thead>
        <tr>
            <th colspan="2">task legend</th>
        </tr>
    </thead>

    <tbody>
    {foreach($colorLegend as $aColor)}
        <tr>
            <td>
                <span style="background-color:{$aColor['color']}; border:0; padding:0; margin:0;">
                    <img src="pixel.gif" width="30" height="10" alt="">
                </span>
            </td>
            <td>{$aColor['name']}</td>
        </tr>
    </tbody>
</table>

<!--
<div name="editWinDiv" style="position:absolute; visibility:hidden;" class="outlineOverlay">
    <table>
        <tr>
            <td colspan="2"><a href="javascript://" onclick="hide('editWinDiv')">X</a></td>
        </tr>
        <tr>
            <td>edit</td>
            <td>{%common_editButton($_SERVER['PHP_SELF'].'?id='.$aTime['id'])%}</td>
        </tr>
        <tr>
            <td>remove</td>
            <td>{%common_removeAndConfirmButton( $_SERVER['PHP_SELF'].'?removeId='.$aTime['id'] )%}</td>
        </tr>
    </table>
</div>
-->

{%common_getJS('common')%}
{%common_getJS('calendar')%}
{%common_getJS($projectTreeJsFile,true)%}

<!--
{%common_getJS('libs/js/classes/env')%}
{%common_getJS('libs/js/classes/func')%}
{%common_getJS('libs/js/classes/object')%}
{%common_getJS('libs/js/classes/object/mouse')%}
{%common_getJS('libs/js/classes/object/events')%}
{%common_getJS('libs/js/classes/object/window')%}
{%common_getJS('libs/js/classes/object/window/frame')%}
-->

<script>
    {if ($doEdit)}
        lastDate = document.editForm["newData[timestamp_date]"].value;
        lastTime = document.editForm["newData[timestamp_time]"].value;

        {if( !$data['id'] )}
            updateTime();

        projectTree.init(true);

    <!--
    function showEditWin(id)
    \{
        setX("editWinDiv",mouse.x);
        setY("editWinDiv",mouse.y);
        show("editWinDiv");
    \}
    -->

    <!--
    editWin = new class_window_frame("editWinDiv");
    //editWin.show();

    editWin.closeable=true;
    editWin.minimizeable=true;
    //editWin.minimizeButtonImage = "http://ludwig/libs/images/window/windowMinimize_BeOS_yellow.gif";
    //editWin.closeButtonImage = "http://ludwig/libs/images/window/windowClose_BeOS_yellow.gif";

    function showEditWin(id)
    \{
        editWin.setX(mouse.x+getScrollOffsetX());
        editWin.setX(mouse.y+getScrollOffsetY());
        editWin.headline ="?erschrift";
        editWin.create();
        editWin.show();
    \}
    -->
</script>
