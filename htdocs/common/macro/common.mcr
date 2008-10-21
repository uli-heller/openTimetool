<!--
    $Log: common.mcr,v $
    Revision 1.21  2003/02/10 16:17:45  wk
    - add new parameter to getJs

    Revision 1.20  2003/01/28 15:19:04  wk
    - remove old macro
    - E_ALL stuff

    Revision 1.19  2003/01/28 10:54:54  wk
    - make the title-attribs translatable

    Revision 1.18  2003/01/13 18:10:55  wk
    - take care of the language when loading the files
    - load file only once per page

    Revision 1.17  2002/12/02 20:20:56  wk
    - set proper image sizes

    Revision 1.16  2002/12/02 15:56:19  wk
    - get the manual from the de-folder for now

    Revision 1.15  2002/11/30 18:36:49  wk
    - confirm to default layout

    Revision 1.14  2002/11/29 15:09:21  wk
    - use switch case

    Revision 1.13  2002/11/29 14:50:34  jv
    - add class for buttons  -

    Revision 1.12  2002/11/29 13:20:16  wk
    - include the common.js as php, since we do translation in there

    Revision 1.11  2002/11/21 19:15:48  wk
    - put show error here
    - made help work properly

    Revision 1.10  2002/11/19 19:55:37  wk
    - explicitly translate

    Revision 1.9  2002/11/07 11:40:54  wk
    - align the help button

    Revision 1.8  2002/10/31 17:43:31  wk
    - show help icon
    - added edit and remove macros

    Revision 1.7  2002/10/24 14:09:55  wk
    - made help use getHelp macro

    Revision 1.6  2002/10/22 18:20:12  wk
    - made getJS more common

    Revision 1.5  2002/10/22 14:23:33  wk
    - added help
    - removed project and task macros from here, which use other macros, see comment on top of file why
    - made dateInput more flexible

    Revision 1.4  2002/08/29 13:28:22  wk
    - add a lot of commonly used macros

    Revision 1.3  2002/08/22 12:41:25  wk
    - put format hint in ()

    Revision 1.2  2002/08/21 20:21:12  wk
    - added JS-macro which includes the necessary JS-file

    Revision 1.1  2002/08/20 16:23:41  wk
    - added the date-input macro

-->
<!--
   
    DONT include other macros here!
    since this file gets included in headline.tpl
    only those macros which are included in there too can be resolved
    other will not be known there, so they can not be parsed and used!!!

-->


<!--
    show an input field with the given and the calendar popup

    @param  string  the input value name
    @param  string  the current value
    @param  string  the name of the form in which the input field will be used
-->
{%macro common_dateInput( $name , $value=0 , $formName='editForm' , $after='(dd.mm.yyyy)' , $setTodayIfEmpty=false )%}
    {global $util}

    <input  name="{$name}"
    value="{echo $value?date('d.m.Y',$util->makeTimestamp($value)):''}"
    size="10"
    onClick="popUpCalendar(this,{$formName}['{$name}'],'dd.mm.yyyy')"
    onBlur="autoCorrectDate('{$formName}','{$name}' , {$setTodayIfEmpty?'true':'false'} )">
    {$T_after}


<!--
    show an input field with the given and the calendar popup for mobile access
    we don't need the calendar there as usually no jscript is working
    so this one is much simpler ......

    @param  string  the input value name
    @param  string  the current value
    @param  string  the name of the form in which the input field will be used
-->
{%macro common_dateInput_mobile( $name , $value=0 , $formName='editForm' , $setTodayIfEmpty=false )%}
    {global $util}

    <input  name="{$name}"
    value="{echo $value?date('d.m.Y',$util->makeTimestamp($value)):''}"
    size="10"
    onBlur="autoCorrectDate('{$formName}','{$name}' , {$setTodayIfEmpty?'true':'false'} )">



<!--
    show an input field for entering a time

    @param  string  the input value name
    @param  string  the current value, as a timestamp
    @param  string  the name of the form in which the input field will be used
-->
{%macro common_timeInput( $name='newData[timestamp_time]' , $value=0 , $formName='editForm' )%}
    {global $util}

    <input name="{$name}"
    value="{echo date('H:i',$value)}"
    size="5"
    onBlur="autoCorrectTime('{$formName}','{$name}')">


<!--
    this macro simply includes the requested js-file with proper headers etc.
-->
{%macro common_getJS( $which='common' , $isPhpJs=false )%}
    {global $config,$tempLoadedJsFiles,$lang}

    <!-- prevent from loading JS-files twice within one page
         first - to reduce traffic
         second - to prevent from JS-errors
         and i am sure i would forget to remove some includes if i would try to correct all the pages
         so i do this, this is secure
     -->
    {if( !@$tempLoadedJsFiles[$which] )}
        { $tempLoadedJsFiles[$which] = true}
        {switch( $which )}
            {case 'calendar':}
            <script type="text/javascript" language="JavaScript" src="{$config->applPathPrefix}/external/calendar/popcalendar.js.php"></script>
            {break;case 'common':}
            <script type="text/javascript" language="JavaScript" src="{$config->applPathPrefix}/common/js/{$which}.js.php"></script>
            {break;default:}
                {if($isPhpJs)}
                    <script type="text/javascript" language="JavaScript" src="{$config->applPathPrefix}/common/js/{$which}.js.php"></script>
                {else}
                    <script type="text/javascript" language="JavaScript" src="{$config->applPathPrefix}/common/js/{$which}.js"></script>
            {break}

<!--
    shows a table row with a textarea for a comment

    @param  string  a value to preset the textarea
    @param  string  the name used for the textarea
-->
{%macro common_commentRow($currentComment='',$name='newData[comment]')%}
    <tr>
        <td>comment</td>
        <td>
            <textarea name="{$name}" cols="50" rows="5">{$currentComment}</textarea>
        </td>
    </tr>


<!--
    shows a table row with a textarea for a comment for mobile devices

    @param  string  a value to preset the textarea
    @param  string  the name used for the textarea
-->
{%macro common_commentRowMobile($currentComment='',$name='newData[comment]')%}
    <tr>
        <td>comment
        <br>
            <textarea name="{$name}" cols="25" rows="5">{$currentComment}</textarea>
        </td>
    </tr>


<!--
    @param  string  the subchapter, since the pageProp->get('manualChapter') mostly gets the right chapter
    @param  string  the string to be shown inside the link
    @param  string  in case u are linking to a chapter, which is not refered to by pageProp->get('manualChapter') give it here
-->
{%macro common_help( $subChapter='' , $string='' , $chapter=null )%}
    {echo common_getHelp( $subChapter , $string , $chapter )}

{%macro common_getHelp( $subChapter='' , $string='' , $chapter=null )%}
    {global $config,$pageProp}

    {if( !$string )}
        { $string='<img src="help.gif" class="button" style="vertical-align:bottom;" width="16px" height="16px" alt="Help">'}


    {if( $chapter == null )}
        { $chapter = $pageProp->get('manualChapter')}


    { $helpUrl = $config->applPathPrefix.'/modules/manual/de/manual.html#'.$chapter.($subChapter&&$subChapter!==true?'_'.$subChapter:'') }

    {return '<a href="javascript://" onClick="openHelpWindow(\''.$helpUrl.'\')" title="Help">'.$string.'</a>'}





{%macro common_editButton( $url='' )%}
    <a title="edit" href="{$url}">
        <img class="button" src="edit.gif" width="16px" height="17px" alt="edit"/>
    </a>


{%macro common_removeAndConfirmButton( $url='' , $text='')%}
    <a title="remove" href="javascript:removeConfirm('{$url}' , '{$text?$text:''}')">
        <img class="button" src="remove.gif" width="16px" height="17px" alt="remove">
    </a>




<!--
    this is actually only needed in the main.tpl
-->
{%macro common_showError( &$configObj )%}

    {if( $configObj->anyErrorOrMessage() )}
        <table class="message" width="100%">
            <tr>
                <th class="message">
                    {if( $configObj->anyError() )}
                        {%common_help(null,null,'errors')%}
                    {else}
                        {%common_help(null,null,'messages')%}
                    &nbsp;Messages
                </th>
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

