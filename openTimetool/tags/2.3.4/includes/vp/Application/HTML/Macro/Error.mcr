<!--
    $Log: Error.mcr,v $
    Revision 1.3  2003/03/11 12:57:55  wk
    *** empty log message ***

    Revision 1.1  2002/11/11 17:50:41  wk
    - initial commit

-->

<!--
    this is actually only needed in the main.tpl
-->
{%macro Error_show( &$configObj )%}
    
    {if( $configObj->anyErrorOrMessage() )}
        <table class="message" width="100%">
            <tr>
                <th class="message">Messages</th>
            </tr>
            <tr>
                <td class="message">
                    {if( $configObj->anyError() )}
                        <font class="warning">
                            {$configObj->getErrors()}
                        </font>
                    {if( $configObj->anyMessage() )}
                        <font class="success">
                            {$configObj->getMessages()}
                        </font>
                </td>
            </tr>
        </table>
        <table><tr><td height="5"></td></tr></table>
