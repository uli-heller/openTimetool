<!--

$Id$

-->

{%include vp/Application/HTML/Macro/EditData.mcr%}
{%include vp/Application/HTML/Macro/Form.mcr%}
{%include vp/Application/HTML/Macro/NextPrev.mcr%}
{%include common/macro/common.mcr%}
{%include common/macro/table.mcr%}
       
<form method="post" action="{$_SERVER['PHP_SELF']}">
    <input type="hidden" name="newData[id]" value="{$data['id']}">
    <table class="outline">
        <thead>
            {%table_headline( (@$data['id']?'edit':'add').' user' , 'user' )%}
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

            {%EditData_input($data,'name',t('first name').' *')%}
            {%EditData_input($data,'surname',t('surname').' *')%}
            {%EditData_input($data,'email',t('email').' *')%}
            {%EditData_input($data,'login',t('login').' *')%}

            {if($config->auth->savePwd || !$data['is_LDAP_user'])} /* hide password fields and reset by using authentication against LDAP */
                {%EditData_password('password',t('password').' ')%}
                {%EditData_password('password1',t('repeat password').' ')%}

                <tr>
                    <td colspan="2">
                        If you check the subsequent option, the user gets a new random password and will be notified by mail.
                    </td>
                </tr>
                <tr>
                    <td>reset password</td>
                    <td>
                        <input type="checkbox" name="newData[ResetPassword]" value="1">
                    </td>
                </tr>

            <tr>
                <td>is admin</td>
                <td>
                    <select name="newData[isAdmin]">
                        {%Form_yesNoOptions($data['isAdmin'])%}
                    </select>
                </td>
            </tr>
            <tr>
                <td>    
                    {%common_help('infoMail')%}
                    send info mail
                </td>
                <td>
                    <input type="checkbox" name="newData[sendInfoMail]" value="1">
                </td>
            </tr>
        </tbody>
    </table>
</form>

<br>

<table class="outline">
    <thead>
        <tr>
            <th>first name</th>
            <th>surname</th>
            <th>email</th>
            <th>login</th>
            <th>admin</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
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
    {foreach( $users as $aUser )}
        <tr id="removeId{$aUser['id']}">
            <td>{$aUser['name']}</td>
            <td>{$aUser['surname']}</td>
            <td>{$aUser['email']}</td>
            <td>{$aUser['login']}</td>
            <td align="center">
                {if($aUser['isAdmin'])}
                    &#10004;
                {else}
                    &nbsp;
            </td>
            <td align="center">
                {%common_editButton($_SERVER['PHP_SELF'].'?id='.$aUser['id'])%}
            </td>
            <td align="center">
                <!-- {%common_removeAndConfirmButton($_SERVER['PHP_SELF'].'?removeId='.$aUser['id'] , t('Are you sure you want to delete this user?\n\nAttention! All data of this user will be irrevocably deleted!') )%} -->
                {%common_removeAndConfirmButtonAlt('user_remove',$aUser['id'])%}
            </td>
        </tr>
    </tbody>
</table>

<script>
    function user_remove(id)
    \{
        var _url = '{$_SERVER['PHP_SELF'].'?removeId='}' + id;
        var _msg = '{echo t('Are you sure you want to delete this user?\n\nAttention! All data of this user will be irrevocably deleted!') }';
        removeConfirm(_url, _msg);
    \}
</script>
         
<!--
<pre>
we need permission for:
- edit project
- edit task
- edit user rights
- view user's times
- view internal/external prices
- remove times, if a user is allowed to remove any of the times he has logged in
- set a status of times that are not allowed to be removed later, because i.e. they
  have already been calculated and worked with in other ways
-

</pre>
-->

{%common_getJS()%}
