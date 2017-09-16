<!--

$Id$

-->

{%include common/macro/common.mcr%}
{%include vp/Application/HTML/Macro/Error.mcr%}

{include($layout->getHeaderTemplate(true))}

<table width="100%" border="0" cellspacing="0" cellpadding="0">
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

{include($layout->getFooterTemplate(true))}
