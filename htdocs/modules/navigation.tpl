<!--

$Id$

-->

{%macro redSquare()%}
    <img src="redDot.gif" alt="" width="8" height="8">

{foreach( $naviItems as $key=>$aItem )}
    {if($aItem['level'] == 0)} <!-- show a root-navi point! such as power user -->
        {if( $key )} <!-- this condition is not true ONLY for the very first itme -->
            </table>
            <br>
        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="navi">

    <tr>
    {if($aItem['level'] == 0)}
        <th nowrap="nowrap" width="99%">
            {$T_aItem['name']}
        </th>
<!--
        <th align="right" nowrap="nowrap">
            <img src="upButton.gif" alt="">
            <img src="downButton.gif" alt="">
        </th>
-->
    {else}
        {if( @$aItem['selected'] )}
            <td colspan="2" class="naviTdWithLink" selected="true" nowrap="nowrap">
        {else}
            <td colspan="2" class="naviTdWithLink" nowrap="nowrap">

        {%repeat $aItem['level']-1 times%}
            &nbsp;

        {if( !@$aItem['macro'] )}
            {%redSquare()%}

        {if( @$aItem['_link'] )}
            {%_buildLink( $aItem )%}
        {else}
            {if( @$aItem['macro'] )}
                {call_user_func($aItem['macro'])}
            {else}
                {$T_aItem['name']}
        </td>
    </tr>
</table>

<br><br>

{%macro _buildLink( $element )%}
    {if( @$element['onClick'] )}
        <a href="javascript://" class="navi" onclick="{$element['onClick']}">{$T_element['name']}</a>
    {if( @$element['url'] )}
        <a href="{$element['url']}" class="navi">{$T_element['name']}</a>



<!-- only activated on the development server! -->
{%macro _chooseLanguage()%}
    {global $config,$lang}

    { $first=true}
    {if($config->hasFeature('translate'))}
        {foreach( $config->availableLanguages as $key=>$aLang )}
            {if($lang != $key)}
                {if( !$first )}
                        </td>
                    </tr>
                        <td colspan="2" class="naviTdWithLink">
                {else}
                    { $first=false}

                <a href="{echo str_replace("/$lang/","/$key/",$_SERVER['PHP_SELF'])}" class="navi">
                    <img src="{$config->vImgRoot}/flags/{$aLang['flag']}.gif" alt="{$aLang['flag']}" height="10">&nbsp;{$aLang['language']}</a>

<!--
    the manual link 
    and the pdf download
-->
{%macro _manualLink()%}
    {global $config}

    {%redSquare()%}
    <a href="javascript://" onclick="openHelpWindow('{$config->vApplRoot}/modules/manual/de/manual.html#intro')" class="navi">Manual</a>
    &nbsp;
    <a href="{$config->vApplRoot}/modules/manual/de/manual.pdf" target="_blank" title="Manual (PDF)"><img src="pdf.gif" width="18" height="18" style="vertical-align:bottom;" alt="Manual (PDF)"></a>
