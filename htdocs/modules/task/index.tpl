<!--

$Id$

-->

{%include vp/Application/HTML/Macro/Form.mcr%}
{%include vp/Application/HTML/Macro/EditData.mcr%}
{%include vp/Application/HTML/Macro/NextPrev.mcr%}
{%include common/macro/common.mcr%}
{%include common/macro/table.mcr%}
        
<form method="post" action="{$_SERVER['PHP_SELF']}">
    <input type="hidden" name="newData[id]" value="{$data['id']}">
    <table class="outline">
        <thead>
            {%table_headline( (isset($data['id'])?'edit':'add').' task' , isset($data['id'])?'edit':'add' )%}
        </thead>

        <tfoot>
            <tr>
                <td>&nbsp;</td>
                <td>
                    <input type="submit" name="action_save" value="Save" class="button">
                    <input type="button" value="Cancel" onclick="window.location='{$_SERVER['PHP_SELF']}'" class="button">
                </td>
            </tr>
        </tfoot>

        <tbody>

            {%EditData_input( $data , 'name' , t('task').' *' )%}

            {%EditData_input( $data , 'comment' , t('comment') )%}

            <tr>
                <td>
                    {%common_help('properties_needsProject')%}
                    needs project?
                </td>
                <td>
                    <select name="newData[needsProject]">
                        {%Form_yesNoOptions($data['needsProject'])%}
                    </select>
                    'no' if this task doesnt require a specific project
                </td>
            </tr>

            <tr>
                <td>
                    {%common_help('properties_calc')%}
                    calculate time?
                </td>
                <td>
                    <select name="newData[calcTime]">
                        {%Form_yesNoOptions($data['calcTime'])%}
                    </select>
                    'yes' if the time for this task shall be calculated
                </td>
            </tr>

            {%EditData_input( $data , 'color' , 'HTML-color' )%}

        </tbody>
    </table>
</form>

<br>
<table class="outline">
    <thead>
        <tr>
            <th>Task</th>
            <th>Comment</th>
            <th>Color</th>
            <th>project</th>
            <th>time</th>
            <th>&nbsp;</th>
            <th>{%common_help('remove')%}</th>
        </tr>
    </thead>

    <tfoot>
        <tr>
            <td align="center" colspan="7">
                <form method="post" action="{$_SERVER['PHP_SELF']}">
                    {%NextPrev_Buttons($nextPrev)%}
                </form>
            </td>
        </tr>
    </tfoot>

    <tbody>
    {foreach( $tasks as $aTask )}
        <tr id="removeId{$aTask['id']}">
            <td nowrap="nowrap">
                {$aTask['name']}
            </td>
            <td valign="top">
                {$aTask['comment']}
            </td>
            <td align="center" valign="middle">
                <span style="background-color:{$aTask['color']};">
                    <img src="pixel.gif" width="20" height="10" alt="">
                </span>
            </td>
            <td align="center">
                {$aTask['needsProject']?'&#10004;':''}
            </td>
            <td align="center">
                {$aTask['calcTime']?'&#10004;':''}
            </td>
            <td align="center">
                {%common_editButton($_SERVER['PHP_SELF'].'?id='.$aTask['id'])%}
            </td>
            <td align="center">
                {%common_removeAndConfirmButton( $_SERVER['PHP_SELF'].'?removeId='.$aTask['id'] )%}
            </td>
        </tr>
    </tbody>
</table>

<!-- script src="{$config->vApplRoot}/common/js/common.js"></script -->
{%common_getJS('common')%}
