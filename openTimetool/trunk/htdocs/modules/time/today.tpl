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


<form method="post" name="editForm" action="{$_SERVER['PHP_SELF']}">
    <input type="hidden" name="newData[id]" value="{$data['id']}">
    <table class="outline">
        {%table_headline('Today-Log')%}
        <tr>
            <td>time</td>
            <td width="100%">
                {%common_dateInput( 'newData[timestamp_date]' , $data['timestamp'] , 'editForm' )%}
                {%common_timeInput( 'newData[timestamp_time]' , $data['timestamp'] )%}
                <input type="button" value="now!" onClick="_updateTime()" class="button">
            </td>
        </tr>
        
        <!-- use '@' to avoid php notices : AK -->
        {%project_row(@$data['projectTree_id'])%}
        {%task_row($tasks,@$data['task_id'])%}
        {%common_commentRow(@$data['comment'])%}

        <tr>
            <td>&nbsp;</td>
            <td>
	            <!-- use isset to avoid php notices : AK -->
                {if( isset($data['id']) )}
                    <input type="submit" name="action_save" value="Update" class="button">
                    <input type="submit" name="action_saveAsNew" value="Save as new" class="button">
                {else}
                    <input type="submit" name="action_saveAsNew" value="Save" class="button">
                <input type="button" value="Cancel" onClick="window.location='{$_SERVER['PHP_SELF']}'" class="button">
            </td>
        </tr>
    </table>
</form>



<br>
{if(sizeof($times))}
	<!-- was $durationSum which seems to be wrong and leads to notice later on where durationSecSum is used ... -->
    { $durationSecSum = 0}
    { $lastUid=-1} <!-- AK set intial state properly -> notice -->
    { $lastDate=-1} <!-- AK set intial state properly -> notice -->
    <table width="100%" class="outline">
        <tr>
            <th>Start</th>
            <th>Comment</th>
            <th>Project</th>
            <th>Task</th>
            <th>Duration</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
        </tr>

        {foreach($times as $aTime)}
            { $class='' }
            {if( $aTime['id'] == @$_REQUEST['id'] )}   <!-- AK : added @ to avoid notices in intial state -->
                { $class = 'class="backgroundHighlight"' }
            {if( $aTime['_user_id'] != $lastUid )}
                <tr>
                    <td valign="top" nowrap colspan="7">
                        <br>
                        <b>User: {$aTime['_user_name']}&nbsp;{$aTime['_user_surname']}</b>
                    </td>
                </tr>

            {if( @$aTime['_longDate'] != $lastDate || $aTime['_user_id'] != $lastUid)} <!-- AK : added @ to avoid notices  -->
                <tr>
                    <td valign="top" colspan="7">
                        <b>{$dateTime->formatDateFull( $aTime['timestamp'] )}</b>
                    </td>
                </tr>

            <tr id="removeId{$aTime['id']}">
                { $lastDate = @$aTime['_longDate']} <!-- AK : added @ to avoid notices  -->
                { $lastUid=$aTime['_user_id']}  <!-- to check this in the date part too -->

                <td valign="top" {$class}>
                    {$dateTime->formatTimeShort($aTime['timestamp'])}
                </td>
                <td valign="top" {$class}>
                    {echo nl2br($aTime['comment'])}
                </td>
                <td valign="top" {$class}>
                    {if($aTime['_task_needsProject'])}
                        {$projectTreeDyn->getPathAsString($aTime['projectTree_id'],' - ')}
                </td>
                <td valign="top" nowrap {$class}>
                    {$aTime['_task_name']}
                </td>
                <td valign="top" nowrap align="center" {$class}>
                    {if(isset($aTime['duration']))} <!-- AK eliminate notice -->           
                        { $durationSecSum+=$aTime['durationSec']}
                        {$aTime['duration']} h
                </td>

                <td valign="top" {$class}>
                    {%common_editButton($_SERVER['PHP_SELF'].'?id='.$aTime['id'])%}
                </td>
                <td valign="top" {$class}>
                    {%common_removeAndConfirmButton( $_SERVER['PHP_SELF'].'?removeId='.$aTime['id'] )%}
                </td>
            </tr>

        <tr>
            <td colspan="7"><br></td>
        </tr>
        <tr>
            <td colspan="4" align="right" valign="top">
                <b>Sum</b>
            </td>
            <td align="center">
                <b>
                {$time->_calcDuration($durationSecSum)} h<br>
                {$time->_calcDuration($durationSecSum,'decimal')} h<br>
                {$time->_calcDuration($durationSecSum,'days')} d
                </b>
            </td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
    </table>






{%common_getJS('common')%}
{%common_getJS('calendar')%}
{%common_getJS($projectTreeJsFile,true)%}
<script type="text/javascript" language="JavaScript">

    lastDate = document.editForm["newData[timestamp_date]"].value;
    lastTime = document.editForm["newData[timestamp_time]"].value;
<!-- use isset to avoid php notices : AK -->
    {if( !isset($data['id']) )}
        updateTime();

    projectTree.init(true);
</script>
