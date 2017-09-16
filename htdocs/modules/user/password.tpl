<!--

$Id$

-->

{%include vp/Application/HTML/Macro/EditData.mcr%}
{%include vp/Application/HTML/Macro/Form.mcr%}
{%include common/macro/common.mcr%}
{%include common/macro/table.mcr%}

<form method="post" action="{$_SERVER['PHP_SELF']}">
    <input type="hidden" name="newData[id]" value="{$data['id']}">
    <input type="hidden" name="newData[name]" value="{$data['name']}">
    <input type="hidden" name="newData[surname]" value="{$data['surname']}">
    <input type="hidden" name="newData[email]" value="{$data['email']}">
    <input type="hidden" name="newData[login]" value="{$data['login']}">
    <input type="hidden" name="newData[newpwd]" value="1">

    <table class="outline">
        <!-- thead>
            {%table_headline('Change Password')%}
        </thead -->

        <tfoot>
            {%EditData_saveButton(false)%}
        </tfoot>

        <tbody>
        {if($config->auth->savePwd || !$data['is_LDAP_user'])}
            {%EditData_password('password',t('password').' ')%}
            {%EditData_password('password1',t('repeat password').' ')%}
        </tbody>
    </table>
</form>

{%common_getJS()%}
