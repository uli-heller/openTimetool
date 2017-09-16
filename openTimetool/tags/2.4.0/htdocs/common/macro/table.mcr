<!--

$Id$

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
