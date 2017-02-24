<!--
    $Id
    
    Revision 1.35.2.4  2006/08/29 20:03:09  ak
    - eliminated php notices

	********** switch to SVN ********
    $Log: index.tpl,v $
    Revision 1.35.2.3  2003/04/10 11:03:09  wk
    - add manual links

    Revision 1.35.2.2  2003/03/21 11:17:42  wk
    - fixed formatting error

    Revision 1.35.2.1  2003/03/19 19:39:36  wk
    - just some nicer layouting

    Revision 1.35  2003/03/04 19:17:04  wk
    - show projectTree via new method

    Revision 1.34  2003/02/18 20:13:40  wk
    - show hours per day

    Revision 1.33  2003/02/17 19:17:09  wk
    - make selecting a project work again

    Revision 1.32  2003/02/10 19:27:17  wk
    - use projectTreeDyn now

    Revision 1.31  2003/02/10 16:14:28  wk
    - comments

    Revision 1.30  2002/12/09 12:20:53  wk
    - added class=button for IE

    Revision 1.29  2002/11/30 18:37:48  wk
    - some unifications

    Revision 1.28  2002/11/30 13:04:10  wk
    - some nicer formatting

    Revision 1.27  2002/11/29 16:56:51  wk
    - show username a bit better
    - use I18N

    Revision 1.26  2002/11/29 14:53:22  jv
    - change placement of extended filter-button -

    Revision 1.25  2002/11/26 15:59:49  wk
    - added extended filter

    Revision 1.24  2002/11/25 10:49:21  wk
    - set window height according to the number of elements

    Revision 1.23  2002/11/22 20:13:09  wk
    - added all help-buttons properly

    Revision 1.22  2002/11/19 20:01:19  wk
    - make day names translateable ... FIXXME do better

    Revision 1.21  2002/11/13 19:01:33  wk
    - some admin handling

    Revision 1.20  2002/11/11 17:58:50  wk
    - moved code from the macro here

    Revision 1.19  2002/11/07 11:43:20  wk
    - added outline class to tables

    Revision 1.18  2002/10/28 11:21:14  wk
    - show other users only if the current user is a project manager

    Revision 1.17  2002/10/24 14:13:46  wk
    - some renaming, and dont use $_REQUEST['id'] anymore

    Revision 1.16  2002/10/22 14:26:54  wk
    - use new macro names

    Revision 1.15  2002/09/11 19:19:16  wk
    - bugfix, dont show current time when editing an entry

    Revision 1.14  2002/09/11 15:50:13  wk
    - IE needs the included js-files before calling any of the methods

    Revision 1.13  2002/09/02 11:29:47  wk
    - made print preview and OO-Export work without the need of the show button to be pressed before

    Revision 1.12  2002/08/29 13:25:47  wk
    - use macros and common.js

    Revision 1.11  2002/08/27 17:47:42  wk
    - use macros for some input fields

    Revision 1.10  2002/08/21 20:22:11  wk
    - show root folder too in edit mode

    Revision 1.9  2002/08/21 17:12:55  wk
    - dont use deprecated macro call anymore

    Revision 1.8  2002/08/20 16:28:04  wk
    - removed JS autoCorrect* functions
    - use macros for date picker

    Revision 1.7  2002/08/14 16:17:35  wk
    - some redesigning

    Revision 1.6  2002/08/14 07:24:35  wk
    - comment

    Revision 1.5  2002/08/05 18:54:56  wk
    - added the OO-Export button

    Revision 1.4  2002/07/30 20:24:11  wk
    - allow multiselects

    Revision 1.3  2002/07/25 11:57:12  wk
    - use autoCorrect
    - started working on the graphical mode

    Revision 1.2  2002/07/24 17:08:29  wk
    - merged former view file in here

    Revision 1.1.1.1  2002/07/22 09:37:37  wk


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
    <form method="post" name="editForm" onSubmit="return confirmSave()">
        <input type="hidden" name="newData[id]" value="{$data['id']}">
        <input type="hidden" name="overBooked" id="overBooked" value="0">
    	<input type="hidden" name="restAvailable" id="restAvailable" value="0">
          <table class="outline">
            {%table_headline('edit entry')%}
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
                    <input type="button" value="now!" onClick="_updateTime()" class="button">
                </td>
            </tr>

            {%project_row($data['projectTree_id'])%}
            {%task_row($tasks,$data['task_id'])%}
            {%common_commentRow($data['comment'])%}

            <tr>
                <td>&nbsp;</td>
                <td>
                    <input type="submit" name="action_save" value="Update" class="button">
                    <input type="submit" name="action_saveAsNew" value="save as new" class="button">
                    <input type="button" value="Cancel" onClick="window.location='{$_SERVER['PHP_SELF']}'" class="button">
                </td>
            </tr>
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
            {%table_headline('filter','filter')%}
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

                    <td valign="top" {$extendedFilter?'width="100%"':''}>

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

            <tr>
                <td colspan="2" align="center">
                    <input type="submit" name="action_show" value="Show"  class="button"/>
                    <input type="submit" name="action_export" value="Export"  class="button"/>
                </td>
            </tr>
        </form>
    </table>



<br>
<!-- AK : use isset instead of sizeof -->
{if(isset($times))}
    { $durationSum = 0}
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
                    <td valign="top" nowrap colspan="7" class="backgroundHighlight">
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
                <td valign="top" nowrap {$class}>
                    {$aTime['_task_name']}
                </td>
                <td valign="top" nowrap align="right" {$class}>
                    <!-- AK : use isset to avoid notices  -->
                    {if(isset($aTime['duration']))}
                        { $durationSecSum+=$aTime['durationSec']}
                        { $secondsPerDay+=$aTime['durationSec']}
                        {$aTime['duration']} h
                </td>

                <td valign="top" {$class}>
                    {if( $aTime['_canEdit'] )}
                        {%common_editButton($_SERVER['PHP_SELF'].'?id='.$aTime['id'])%}
                    {else}
                        &nbsp;
                </td>
                <td valign="top" {$class}>
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
    </table>


<div id="msgxajax" name="msgxajax"></div>

<script type="text/javascript" language="JavaScript">
    function confirmSave()
    \{
		bookDate = document.editForm["newData[timestamp_date]"].value;
		bookTime = document.editForm["newData[timestamp_time]"].value;
		taskId = document.editForm["newData[task_id]"].value;
		projectTreeId = document.editForm["newData[projectTree_id]"].value;
		oldid = document.editForm["newData[id]"].value;

		xajax.call( 'checkBookings', \{ mode:'synchronous', parameters:[projectTreeId,taskId,oldid,bookDate,bookTime] \} );
		
		if(document.editForm["overBooked"].value && document.editForm["overBooked"].value != "0") \{
			neg = document.editForm["restAvailable"].value.indexOf('-');
			if(neg != -1) \{
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
<script type="text/javascript" language="JavaScript">

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
