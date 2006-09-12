<!--
	$Id

	The template for the asp-account request. Not used in usual net-Mode.
	
	These macros are expanded by the template engine.

	**** switch to SVN ****
    $Log: index.tpl,v $
    Revision 1.2  2002/11/29 16:59:21  wk
    - do asp-account request

    Revision 1.1.1.1  2002/07/22 09:37:37  wk


//-->

{%include vp/Application/HTML/Macro/EditData.mcr%}
{%include common/macro/common.mcr%}
{%include common/macro/table.mcr%}

<form action="{$_SERVER['PHP_SELF']}" method="post">
    <table class="outline">

        {%table_headline('account name')%}
        {%EditData_input($data,'accountName','account name')%}
        <tr> 
            <td>&nbsp;</td>
            <td>
                <input type="submit" value="OK">
            </td>
        </tr>

    </table>
</form>