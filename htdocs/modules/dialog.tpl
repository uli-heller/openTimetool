<!--
    $Log: dialog.tpl,v $
    Revision 1.7  2002/12/01 13:33:31  wk
    - added the br at the end

    Revision 1.6  2002/11/22 20:14:30  wk
    - use common_showError

    Revision 1.5  2002/11/11 17:59:38  wk
    - show errors properly

    Revision 1.4  2002/10/22 14:42:59  wk
    - show page header

    Revision 1.3  2002/07/24 17:09:27  wk
    - show all errors

    Revision 1.2  2002/07/22 12:03:49  wk
    - show errors

    Revision 1.1.1.1  2002/07/22 09:37:37  wk


-->

{%include common/macro/common.mcr%}
{%include vp/Application/HTML/Macro/Error.mcr%}


{include($layout->getHeaderTemplate(true))}

<br>
<center>
    <table width="95%">
        {if($pageProp->get('pageHeader'))}
            <tr>
                <td class="pageHeader" width="99%">{$T_pageProp->get('pageHeader')}</td>
                <td class="pageHeader" nowrap="nowrap" align="right">
                    {%common_help()%}
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
        <tr>
            <td colspan="2">      
                {%common_showError( $config )%}
                {include($layout->getContentTemplate('',true))}
                <br>
            </td>
        </tr>
    </table>
</center>   
<br>

{include($layout->getFooterTemplate(true))}
