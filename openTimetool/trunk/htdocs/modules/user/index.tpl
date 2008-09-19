<!--
	$Id
	
	Revision 1.15 ak
	- password reset checkbox

    $Log: index.tpl,v $
    Revision 1.14  2003/02/13 16:19:20  wk
    - send info mail field

    Revision 1.13  2002/12/09 13:50:25  wk
    - added some required classes and button

    Revision 1.12  2002/12/05 14:20:09  wk
    - make some fields required

    Revision 1.11  2002/12/02 20:22:14  wk
    - translate

    Revision 1.10  2002/11/30 13:05:04  wk
    - add password stuff if needed

    Revision 1.9  2002/11/29 16:57:12  wk
    - translate text

    Revision 1.8  2002/11/22 20:13:22  wk
    - bug fix

    Revision 1.7  2002/11/19 20:01:48  wk
    - use first name instead of name only

    Revision 1.6  2002/10/31 17:48:25  wk
    - use buttons

    Revision 1.5  2002/10/28 16:24:09  wk
    - let admin change user rights, etc.

    Revision 1.4  2002/10/24 14:14:37  wk
    - moved boxes around to have a unique layout

    Revision 1.3  2002/09/02 11:29:15  wk
    - added previous next logic

    Revision 1.2  2002/08/30 18:45:28  wk
    - implemented proper user editing

    Revision 1.1  2002/08/20 09:02:57  wk
    - initial commit

-->

{%include vp/Application/HTML/Macro/EditData.mcr%}
{%include vp/Application/HTML/Macro/Form.mcr%}
{%include vp/Application/HTML/Macro/NextPrev.mcr%}
{%include common/macro/common.mcr%}
{%include common/macro/table.mcr%}
       
<form method="post" action="{$_SERVER['PHP_SELF']}">
    <input type="hidden" name="newData[id]" value="{$data['id']}">
    <table class="outline">
        {%table_headline( (@$data['id']?'edit':'add').' user' , 'user' )%}

        {%EditData_input($data,'name',t('first name').' *')%}
        {%EditData_input($data,'surname',t('surname').' *')%}
        {%EditData_input($data,'email',t('email').' *')%}
        {%EditData_input($data,'login',t('login').' *')%}

        {if($config->auth->savePwd)}
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
                <input type="checkbox" name="newData[ResetPassword]" value="1"/>
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
                <input type="checkbox" name="newData[sendInfoMail]" value="1"/>
            </td>
        </tr>
        {%EditData_saveButton(false)%}

    </table>
</form>

<br>

<table class="outline" width="90%">
    <tr>
        <th>first name</th>
        <th>surname</th>
        <th>email</th>
        <th>login</th>
        <th>admin</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
    </tr>

    {foreach( $users as $aUser )}
        <tr id="removeId{$aUser['id']}">
            <td>{$aUser['name']}</td>
            <td>{$aUser['surname']}</td>
            <td>{$aUser['email']}</td>
            <td>{$aUser['login']}</td>
            <td align="center">
                {if($aUser['isAdmin'])}
                    *
                {else}
                    &nbsp;
            </td>
            <td>
                {%common_editButton($_SERVER['PHP_SELF'].'?id='.$aUser['id'])%}
            </td>
            <td>
                {%common_removeAndConfirmButton( $_SERVER['PHP_SELF'].'?removeId='.$aUser['id'] , t('Are you sure you want to delete this user?') )%}
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
