<!--
    $Log: navigation.tpl,v $
    Revision 1.13  2003/01/29 10:39:48  wk
    - E_ALL stuff

    Revision 1.12  2003/01/28 10:57:56  wk
    - manual is a macro, so we can show the pdf-link behind

    Revision 1.11  2003/01/13 18:13:16  wk
    - change the language link to use subdirs

    Revision 1.10  2002/12/02 20:22:24  wk
    - set proper image sizes

    Revision 1.9  2002/11/30 13:06:26  wk
    - prepare for filterLevel 10

    Revision 1.8  2002/11/19 20:02:39  wk
    - explicitly translate

    Revision 1.7  2002/10/28 11:21:34  wk
    - added nowrap

    Revision 1.6  2002/10/22 14:44:48  wk
    - use vp-navi

    Revision 1.5  2002/09/11 15:51:36  wk
    - renamed pics to have proper names
    - show flags

    Revision 1.4  2002/08/27 08:51:48  wk
    - show scrollbars too

    Revision 1.3  2002/08/20 16:30:35  wk
    - design 2nd sublevel

    Revision 1.2  2002/08/14 16:19:33  wk
    - removed old navi-stuff
    - added menu item 'prices'

    Revision 1.1.1.1  2002/07/22 09:37:37  wk


-->

{%macro redSquare()%}
    <img src="redDot" alt="" border="0" width="8px" height="8px">


{foreach( $naviItems as $key=>$aItem )}
    {if($aItem['level'] == 0)}  <!-- show a root-navi point! such as power user -->
        {if( $key )}    <!-- this condition is not true ONLY for the very first itme -->
            </table>
            <br>
        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="navi">

    <tr>
        {if($aItem['level'] == 0)}
            <th nowrap="nowrap" width="99%">
                {$T_aItem['name']}
            </th>
<!--
            <th align="right" nowrap>
                <img src="upButton.gif" border="0">
                <img src="downButton.gif" border="0">
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
        <a href="javascript://" class="navi" onClick="{$element['onClick']}">{$T_element['name']}</a>
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
                    <img src="{$config->vImgRoot}/flags/{$aLang['flag']}.gif" border="0" height="10">&nbsp;{$aLang['language']}</a>

<!--
    the manual link 
    and the pdf download
-->
{%macro _manualLink()%}
    {global $config}

    {%redSquare()%}
    <a href="javascript://" onClick="openHelpWindow('{$config->vApplRoot}/modules/manual/de/manual.html#intro')" class="navi">Manual</a>
    &nbsp;
    <a href="{$config->vApplRoot}/modules/manual/de/manual.pdf" target="_blank" title="Manual (PDF)"><img src="pdf.gif" border="0" style="vertical-align:bottom" alt="Manual (PDF)"></a>


