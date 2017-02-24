<!--
    $Log: table.mcr,v $
    Revision 1.4  2002/11/29 14:49:53  jv
    - change order of info-button and text  -

    Revision 1.3  2002/11/22 20:09:16  wk
    - change default subTopic value

    Revision 1.2  2002/11/19 19:55:45  wk
    - explicitly translate

    Revision 1.1  2002/10/24 14:09:31  wk
    - initial commit

-->      
                             
<!--
    this macro is mainly here so the table headline can be translated properly
    if i had added the help link to the headline the translation wont work
-->
{%macro table_headline( $text , $helpSubTopic=true )%}
    <tr>
        <th colspan="2">            
            {if( $helpSubTopic )}
                {%common_help($helpSubTopic)%}&nbsp;
            {$T_text}
        </th>
    </tr>

                                 
<!--
    this macro is mainly here so the table headline can be translated properly
    if i had added the help link to the headline the translation wont work
    this version is for mobile access : only one column
-->
{%macro table_headline_mobile( $text , $helpSubTopic=true )%}
    <tr>
        <th colspan="1">            
            {if( $helpSubTopic )}
                {%common_help($helpSubTopic)%}&nbsp;
            {$T_text}
        </th>
    </tr>