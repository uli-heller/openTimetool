<!--
    $Log: password.tpl,v $

    Revision 1.0  2007/09/12 09:02:57  ak
    - initial commit
    
    This one is for user to change password

	To be as simple as possible, we have all data of the user as hidden form 
	fields in here. Then we can just use the standard save function of the 
	pagehandler -> class 'user.php' 
	= very similar to the admin function for editing users 

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
        <!--{%table_headline('Change Password')%}-->

        {if($config->auth->savePwd)}
            {%EditData_password('password',t('password').' ')%}
            {%EditData_password('password1',t('repeat password').' ')%}

        {%EditData_saveButton(false)%}

    </table>
</form>

{%common_getJS()%}
