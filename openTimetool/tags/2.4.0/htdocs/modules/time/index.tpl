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

{if(isset($data['id']))}
    <!--
     |
     |  edit entry
     |
     +-->
    <form method="post" name="editForm" onsubmit="return confirmSave()">
        <input type="hidden" name="newData[id]" value="{$data['id']}">
        <input type="hidden" name="overBooked" id="overBooked" value="0">
    	<input type="hidden" name="restAvailable" id="restAvailable" value="0">
        <table class="outline">
            <thead>
                {%table_headline('edit entry')%}
            </thead>

            <tfoot>
                <tr>
                    <td>&nbsp;</td>
                    <td>
                        <input type="submit" name="action_save" value="Update" class="button">
                        <input type="submit" name="action_saveAsNew" value="save as new" class="button">
                        <input type="button" value="Cancel" onclick="window.location='{$_SERVER['PHP_SELF']}'" class="button">
                    </td>
                </tr>
            </tfoot>

            <tbody>
                <tr>
                    <td>User</td>
                    <td>
                        {if( $isAdmin )}
                            <select name="newData[user_id]">
                                {%user_asOptions( $users , $data['user_id'] )%}
                            </select>
                        {else}
                            <input type="hidden" name="newData[user_id]" value="{$data['user_id']}">
                            {$data['_user_name']}&nbsp;{$data['_user_surname']}
                    </td>
                </tr>
                <tr>
                    <td>time</td>
                    <td>
                        {%common_dateInput( 'newData[timestamp_date]' , $data['timestamp'] , 'editForm' )%}
                        {%common_timeInput( 'newData[timestamp_time]' , $data['timestamp'] )%}
                        <input type="button" value="now!" onclick="_updateTime()" class="button">
                    </td>
                </tr>

                {%project_row($data['projectTree_id'])%}
                {%task_row($tasks,$data['task_id'])%}
                {%common_commentRow($data['comment'])%}

            </tbody>
        </table>
    </form>
{else}
    <!--
     |
     |  this is the filter view
     |
     +-->
    <table>
        <form method="post" name="showForm" action="{$_SERVER['PHP_SELF']}">
            <thead>
                {%table_headline('filter','filter')%}
            </thead>

            <tfoot>
                <tr>
                    <td colspan="2" align="center">
                        <input type="submit" name="action_show" value="Show"  class="button">
                        <input type="submit" name="action_export" value="Export"  class="button">
                    </td>
                </tr>
            </tfoot>

            <tbody>
                <tr>
                    <td valign="top">
                        <table class="outline">
                            <tr>
                                <td>from</td>
                                <td>
                                    {%common_dateInput( 'show[humanDateFrom]' , $show['dateFrom'] , 'showForm' )%}
                                </td>
                            </tr>
                            <tr>
                                <td>until</td>
                                <td>
                                    {%common_dateInput( 'show[humanDateUntil]' , $show['dateUntil'] , 'showForm' )%}
                                </td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td nowrap="nowarp">
                                    <input type="submit" name="action_showWeekMinus1" value=" &lt;&lt; " class="button">
                                    <input type="submit" name="action_showDayMinus1" value=" &lt; " class="button">
                                    <input type="submit" name="action_showToday" value="today" class="button">
                                    <input type="submit" name="action_showDayPlus1" value=" &gt; " class="button">
                                    <input type="submit" name="action_showWeekPlus1" value=" &gt;&gt; " class="button">
                                </td>
                            </tr>

                            {if( $extendedFilter )}
                                {if( $isManager || $isAdmin )} /* 20081112, jv: display all user also for admins */
                                    <tr>
                                        <td>User</td>
                                        <td>
                                            <select name="show[user_ids][]" multiple size="5">
                                                {%usersAsOptions($users,$show['user_ids'])%}
                                            </select>
                                        </td>
                                    </tr>
                                {else}
                                    <tr>
                                        <td colspan="2">&nbsp;</td>
                                    </tr>

                                <tr>
                                    <td>Comment</td>
                                    <td>
                                        <input name="show[comment]" value="{@$show['comment']}">
                                    </td>
                                </tr>                            
                        </table>
                    </td>

                    <td valign="top">
                        <table class="outline" {$extendedFilter?'width="100%"':''}>
                        {if( $extendedFilter )}
                            <tr>
                                <td colspan="2">
                                    <table cellspacing="0" cellpadding="0" border="0">
                                        <tr>
                                            <td>
                                                <input type="submit" name="action_extendedFilter" value="extended filter OFF" class="button">
                                            </td>
                                            <td>{%common_help('extendedFilter')%}</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            {%project_row(@$show['projectTree_ids'],'show[projectTree_ids][]')%}
                            <tr>
                                <td>Task</td>
                                <!-- set the width, so when selecting a project the table stays as it is -->
                                <td width="90%">
                                    <select name="show[task_ids][]" multiple size="5">
                                        {%tasksAsOptions($tasks,$show['task_ids'])%}
                                    </select>
                                </td>
                            </tr>
                        {else}
                            <tr>
                                <td colspan="2">
                                    <table cellspacing="0" cellpadding="0" border="0">
                                        <tr>
                                            <td>
                                                <input type="submit" name="action_extendedFilter" value="extended filter" class="button">
                                            </td>
                                            <td>{%common_help('extendedFilter')%}</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>
        </form>
    </table>

<br>
<!-- AK : use isset instead of sizeof -->
{if(isset($times))}
    { $durationSum = 0}
    <table width="100%" class="outline">
        <thead>
            <tr>
                <th>Start</th>
                <th>Comment</th>
                <th>Project</th>
                <th>Task</th>
                <th>Duration</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
        </thead>

        <tbody>
            <tr> 
                <td colspan="7"><br></td>
            </tr>

            { $secondsPerDay=0}
            <!-- set these vars to an initial empty value to avoid notices -->
            { $durationSecSum=0}
            { $lastUid=-1}
            { $lastDate=0}        
            {foreach($times as $aTime)}
                { $class='' }
                {if( $aTime['id'] == @$_REQUEST['id'] )}
                    { $class = 'class="backgroundHighlight"' }
                { $dayEnds = (date('dmY',$aTime['timestamp'])!=$lastDate || $aTime['_user_id']!=$lastUid)}

                {if ($dayEnds)}
                    <!-- summary of hours per day! -->
                    {if ($lastDate)}
                        <tr>
                            <td colspan="4" align="right" valign="top">&nbsp;</td>
                            <td align="right">
                                <b>{$time->_calcDuration($secondsPerDay)} h</b>
                            </td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        { $secondsPerDay=0}

                {if( $aTime['_user_id'] != $lastUid )}
                    <tr>
                        <td valign="top" nowrap="nowarp" colspan="7" class="backgroundHighlight">
                            <b>User: {$aTime['_user_name']}&nbsp;{$aTime['_user_surname']}</b>
                        </td>
                    </tr>

                {if ($dayEnds)}
                    <tr>
                        <td valign="top" colspan="7">
                            <b>{$dateTime->formatDateFull($aTime['timestamp'])}</b>
                        </td>
                    </tr>

                <tr id="removeId{$aTime['id']}">
                    { $lastDate = date('dmY',$aTime['timestamp'])}
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
                    <td valign="top" nowrap="nowarp" {$class}>
                        {$aTime['_task_name']}
                    </td>
                    <td valign="top" nowrap="nowarp" align="right" {$class}>
                        <!-- AK : use isset to avoid notices  -->
                        {if(isset($aTime['duration']))}
                            { $durationSecSum+=$aTime['durationSec']}
                            { $secondsPerDay+=$aTime['durationSec']}
                            {$aTime['duration']} h
                    </td>

                    <td align="center" valign="top" {$class}>
                        {if( $aTime['_canEdit'] )}
                            {%common_editButton($_SERVER['PHP_SELF'].'?id='.$aTime['id'])%}
                        {else}
                            &nbsp;
                    </td>
                    <td align="center" valign="top" {$class}>
                        {if( $aTime['_canEdit'] )}
                            {%common_removeAndConfirmButton( $_SERVER['PHP_SELF'].'?removeId='.$aTime['id'] )%}
                        {else}
                            &nbsp;
                    </td>
                </tr>

            <tr>
                <td colspan="4" align="right" valign="top">&nbsp;</td>
                <td align="right">
                    <b>{$time->_calcDuration($secondsPerDay)} h</b>
                </td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td colspan="7"><br></td>
            </tr>
        </tbody>

        <tfoot>
            <tr>
                <td colspan="4" align="right" valign="top">
                    <b>Sum</b>
                </td>
                <td align="right" nowrap="nowrap">
                    <b>
                    {$time->_calcDuration($durationSecSum)} h<br>
                    {$time->_calcDuration($durationSecSum,'decimal')} h<br>
                    {$time->_calcDuration($durationSecSum,'days')} d
                    </b>
                </td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </tfoot>
    </table>

<div id="msgxajax" name="msgxajax"></div>

<script>
    function confirmSave()
    \{
	bookDate = document.editForm["newData[timestamp_date]"].value;
	bookTime = document.editForm["newData[timestamp_time]"].value;
	taskId = document.editForm["newData[task_id]"].value;
	projectTreeId = document.editForm["newData[projectTree_id]"].value;
	oldid = document.editForm["newData[id]"].value;

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

{%common_getJS('common')%}
{%common_getJS('calendar')%}
{%common_getJS($projectTreeJsFile,true)%}

<script>
    // AK : use isset to avoid notices
    function openExportWindow()
    \{
        openWindowOnce("{$config->vApplRoot}/modules/export/index.php",
                        "Export",
                        "left=0,top=0,width=600,height={$exportWinHeight},resizable=yes,scrollbars=yes",
                        "export");
    \}

    // if 'Export' was pushed
    {if( isset($_REQUEST['action_export']) )}
        openExportWindow();

    {if(isset($data['id']))}
        projectTree.init(true);
    {elseif($extendedFilter)}
        projectTree.init(true,"show[projectTree_ids][]");
</script>
