<!--
    $Log: index.tpl,v $
    Revision 1.11.2.1  2003/03/11 16:07:18  wk
    - add cancel button

    Revision 1.11  2002/12/13 10:08:21  wk
    - uups translate bug

    Revision 1.10  2002/12/09 13:50:25  wk
    - added some required classes and button

    Revision 1.9  2002/11/29 14:52:12  jv
    - small layout changes  -

    Revision 1.8  2002/11/22 20:12:34  wk
    - put the help buttons first

    Revision 1.7  2002/11/19 20:00:26  wk
    - translate strings explicitly

    Revision 1.6  2002/10/31 17:48:25  wk
    - use buttons

    Revision 1.5  2002/10/24 14:13:09  wk
    - some renaming and showing proper headline

    Revision 1.4  2002/09/02 11:29:15  wk
    - added previous next logic

    Revision 1.3  2002/08/30 18:44:49  wk
    - post properly and enable highlighting

    Revision 1.2  2002/08/20 09:05:16  wk
    - use macros
    - add remove button

    Revision 1.1.1.1  2002/07/22 09:37:37  wk


-->
{%include vp/Application/HTML/Macro/Form.mcr%}
{%include vp/Application/HTML/Macro/EditData.mcr%}
{%include vp/Application/HTML/Macro/NextPrev.mcr%}
{%include common/macro/common.mcr%}
{%include common/macro/table.mcr%}
        
<form method="post" action="{$_SERVER['PHP_SELF']}">
    <input type="hidden" name="newData[id]" value="{@$data['id']}">
    <table class="outline">

        {%table_headline( (isset($data['id'])?'edit':'add').' task' , isset($data['id'])?'edit':'add' )%}

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

        <tr>
            <td>&nbsp;</td>
            <td>
                <input type="submit" name="action_save" value="Save" class="button">
                <input type="button" value="Cancel" onClick="window.location='{$_SERVER['PHP_SELF']}'" class="button">
            </td>
        </tr>

    </table>
</form>

<br>
<table class="outline">
    <tr>
        <th>Task</th>
        <th>Comment</th>
        <th>Color</th>
        <th>project</th>
        <th>time</th>
        <th>&nbsp;</th>
        <th>{%common_help('remove')%}</th>
    </tr>
    {foreach( $tasks as $aTask )}
        <tr id="removeId{$aTask['id']}">
            <td nowrap>
                {$aTask['name']}
            </td>
            <td valign="top">
                {$aTask['comment']}
            </td>
            <td align="center" valign="center">
                <span style="background-color:{$aTask['color']};">
                    <img src="pixel" width="20" height="10">
                </span>
            </td>
            <td align="center">
                {$aTask['needsProject']?'*':''}
            </td>
            <td align="center">
                {$aTask['calcTime']?'*':''}
            </td>
            <td>
                {%common_editButton($_SERVER['PHP_SELF'].'?id='.$aTask['id'])%}
            </td>
            <td>
                {%common_removeAndConfirmButton( $_SERVER['PHP_SELF'].'?removeId='.$aTask['id'] )%}
            </td>
        </tr>
    <tr>
        <td align="center" colspan="7">
            <form method="post" action="{$_SERVER['PHP_SELF']}">
                {%NextPrev_Buttons($nextPrev)%}
            </form>
        </td>
    </tr>
</table>

<script type="text/javascript" language="JavaScript" src="{$config->vApplRoot}/common/js/common.js"></script>
