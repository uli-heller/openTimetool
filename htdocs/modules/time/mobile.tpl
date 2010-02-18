<!--
    $Id

	mobile : copy of quick for access by mobile phones and pdas
	
	We eliminated the edit part from form as we don't want to have it here.
	See comment block at beginning of form below
	
	Well seems to be cool : by just adding php&tpl without any other entries in any 
	arrays or classes, I'll get that page without navigation and stuff I don't need
	Comes because MainLayout is "modules/dialog" set in mobile.php. Same as quick_log
	we copied from
	Well in init.php I defined the page-header. looks better ...
	quick_log is defined as popup-window in modules/navigation.php by the way. As
	we don't have mobile there, it doesn't appear in menu

-->

{%include vp/Application/HTML/Macro/Tree.mcr%}
{%include common/macro/task.mcr%}
{%include common/macro/project.mcr%}
{%include common/macro/time.mcr%}
{%include common/macro/common.mcr%}
{%include common/macro/table.mcr%}

<center>

<form method="post" name="editForm" action="{$_SERVER['PHP_SELF']}">
    <input type="hidden" name="newData[user_id]" value="{$data['user_id']}">
    <table width="100%" class="outline">
        {%table_headline_mobile( 'Logging' , false )%}
        <tr>
            <td>date, time
            	<br>
                {%common_dateInput_mobile( 'newData[timestamp_date]' , $data['timestamp'] , 'editForm' )%}
                <br>
                <input name="newData[timestamp_time]" value="{echo date('H:i',$data['timestamp'])}" size="5" onBlur="autoCorrectTime('editForm','newData[timestamp_time]')">
                <!-- update with client time -->
                <!-- <input type="button" class="button" value="refresh" onClick="_updateTime()"> -->
                <!-- currently active: update with server time --> 
                <input type="button" class="button" value="refresh" onClick="location.reload()">
            </td>
        </tr>

        <!--{%project_row($lastTime['projectTree_id'])%}-->
        
        <tr>
            <td>project
            <br>       
		        <select name="newData[projectTree_id]">
        	       {%Tree_asOptions($allFolders,$newData[projectTree_id])%}
        		</select>
        	</td>
        </tr>
        
        {%task_row_mobile($tasks,$lastTime['task_id'])%}
        {%common_commentRowMobile($lastTime['comment'])%}

        <tr>
            <td colspan="1" align="center">
                <input type="submit" class="button" name="action_saveAsNew" value="save">
            </td>
        </tr>
    </table>
</form>
</center>


{%common_getJS('common')%}
{%common_getJS('calendar')%}
