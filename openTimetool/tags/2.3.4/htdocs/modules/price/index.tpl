<!--
    $Log: index.tpl,v $
    Revision 1.8  2002/10/31 17:48:25  wk
    - use buttons

    Revision 1.7  2002/10/24 14:11:58  wk
    - some renaming and showing proper headline

    Revision 1.6  2002/09/02 11:29:15  wk
    - added previous next logic

    Revision 1.5  2002/08/30 18:43:59  wk
    - price uses the proper join, so we can simplify this here
    - add tr-id for highlighting

    Revision 1.4  2002/08/26 09:43:12  wk
    - added comment

    Revision 1.3  2002/08/26 09:08:36  wk
    - generate price-name

    Revision 1.2  2002/08/21 20:21:43  wk
    - made it work

    Revision 1.1  2002/08/20 09:02:13  wk
    - initial commit

-->

{%include vp/Application/HTML/Macro/Tree.mcr%}
{%include vp/Application/HTML/Macro/Form.mcr%}
{%include vp/Application/HTML/Macro/EditData.mcr%}
{%include vp/Application/HTML/Macro/NextPrev.mcr%}
{%include common/macro/user.mcr%}
{%include common/macro/task.mcr%}
{%include common/macro/project.mcr%}
{%include common/macro/common.mcr%}
{%include common/macro/table.mcr%}

<font class="warning">ACHTUNG: die Zuordnung der Preise zu den Projekten funktioniert noch nicht!!!
Bisher werden nur die Preise die direkt NUR einer T&auml;tigkeit zugeordnet sind berechnet!</font>

<form method="post" name="editForm">
    <input type="hidden" name="newData[id]" value="{$data['id']}">
    <table class="outline">

        {%table_headline( ($data['id']?'edit':'add').' price' , $data['id']?'edit':'add' )%}

        <tr>
            <td>description</td>
            <td>
                <input name="newData[name]" value="{$data['name']}" size="30">
                <input type="button" value="make" onClick="makeName()">
            </td>
        </tr>


        {%EditData_textarea( $data , 'comment' )%}

        <tr>
            <td>user</td>
            <td>
                <select name="newData[user_id]">
                    <option value=""></option>
                    {%usersAsOptions( $users , $data['user_id'] )%}
                </select>
            </td>
        </tr>

        <tr>
            <td>project</td>
            <td>                                  
                <select name="newData[projectTree_id]">
                    <option value=""></option>
                    {%Tree_asOptions( $projects , $data['projectTree_id'] )%}
                </select>
            </td>
        </tr>

        <tr>
            <td>task</td>
            <td>
                <select name="newData[task_id]">
                    {%tasksAsOptions( $tasks , $data['task_id'] )%}
                </select>
            </td>
        </tr>

        <tr>
            <td>valid from</td>
            <td>
                {%common_dateInput( 'newData[validFrom]' , $data['validFrom'] , 'editForm' )%} until
                {%common_dateInput( 'newData[validUntil]' , $data['validUntil'] , 'editForm' )%}
                <br>
                leave empty if the price is always valid
            </td>
        </tr>

        <tr>
            <td>internal</td>
            <td>
                <input name="newData[internal]" size="6" value="{$data['internal']}"> &euro;
            </td>
        </tr>

        <tr>
            <td>external</td>
            <td>
                <input name="newData[external]" size="6" value="{$data['external']}"> &euro;
            </td>
        </tr>

        {%EditData_saveButton(false)%}

    </table>
</form>

<br>

<table class="outline">
    <tr>
        <th>description</th>
        <th>comment</th>
        <th>user</th>
        <th>project</th>
        <th>task</th>
        <th>valid from</th>
        <th>until</th>
        <th>internal</th>
        <th>external</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
    </tr>
    {foreach( $prices as $aPrice )}
        <tr id="removeId{$aPrice['id']}">
            <td>{$aPrice['name']}</td>
            <td>{$aPrice['comment']}</td>
            <td>{$aPrice['_user_name']} {$aPrice['_user_surname']}</td>
            <td>{$aPrice['project_id']}</td>
            <td>{$aPrice['_task_name']}</td>
            <td>
                {if($aPrice['validFrom'])}
                    {$util->convertTimestamp($aPrice['validFrom'])}
            </td>
            <td>
                {if($aPrice['validUntil'])}
                    {$util->convertTimestamp($aPrice['validUntil'])}
            </td>
            <td align="right">{$aPrice['internal']} &euro; &nbsp;</td>
            <td align="right">{$aPrice['external']} &euro; &nbsp;</td>
            <td>
                {%common_editButton($_SERVER['PHP_SELF'].'?id='.$aPrice['id'])%}
            </td>
            <td>
                {%common_removeAndConfirmButton( $_SERVER['PHP_SELF'].'?removeId='.$aPrice['id'] )%}
            </td>
        </tr>
    <tr>
        <td align="center" colspan="11">
            <form method="post" action="{$_SERVER['PHP_SELF']}">
                {%NextPrev_Buttons($nextPrev)%}
            </form>
        </td>
    </tr>
</table>



<br><br><br>
<pre>
we need prices for:
- each user and a project and task combination
  (one day we should build groups which have equal prices, like: programmers, project managers, etc.)
- with the limitation when the price starts to be valid, which means
  the price used before will be invalid after 'validFrom'
  this way the price is flexibly changeable

</pre>

<script type="text/javascript" language="JavaScript">
    function makeName()
    \{
        if( document.editForm["newData[projectTree_id]"][document.editForm["newData[projectTree_id]"].selectedIndex].text )
        \{
            document.editForm["newData[name]"].value =
                document.editForm["newData[projectTree_id]"][document.editForm["newData[projectTree_id]"].selectedIndex].text+" / "+
                document.editForm["newData[task_id]"][document.editForm["newData[task_id]"].selectedIndex].text;
        \}
        else
        \{
            document.editForm["newData[name]"].value =
                document.editForm["newData[task_id]"][document.editForm["newData[task_id]"].selectedIndex].text;
        \}
        // remove leading ' - - ' etc.
        document.editForm["newData[name]"].value = document.editForm["newData[name]"].value.replace(/^(\s*-)*\s*/,"");
        if( document.editForm["newData[validFrom]"].value )
            document.editForm["newData[name]"].value += " ("+document.editForm["newData[validFrom]"].value+")";
    \}
</script>

<script type="text/javascript" language="JavaScript" src="{$config->vApplRoot}/common/js/common.js"></script>
{%common_getJS('calendar')%}

