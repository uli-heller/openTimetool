<!--
    $Id
    
    Revisione 1.27 2009/22/01 AK
    - added remove button
    
    Revision 1.26.2.3  2006/08/31 16:26:17  AK
    - php notice eliminations

    $Log: index.tpl,v $
    Revision 1.26.2.2  2003/03/17 16:26:17  wk
    - format maxDuration col

    Revision 1.26.2.1  2003/03/11 16:07:11  wk
    - replace "through" by "-" since it was not translated

    Revision 1.26  2003/03/04 19:15:10  wk
    - use I18N to format dates

    Revision 1.25  2003/02/18 20:13:22  wk
    - add maxDuration field

    Revision 1.24  2002/12/11 12:05:32  wk
    - show round and close only if given

    Revision 1.23  2002/12/11 11:24:52  wk
    - added js-classes

    Revision 1.22  2002/12/09 13:50:25  wk
    - added some required classes and button

    Revision 1.21  2002/12/01 14:04:44  wk
    - added missing space

    Revision 1.20  2002/11/29 14:51:50  jv
    - small layout changes  -

    Revision 1.19  2002/11/22 20:12:34  wk
    - put the help buttons first

    Revision 1.18  2002/11/19 20:00:07  wk
    - check if feature price is on and show only depending on it

    Revision 1.17  2002/11/13 19:00:47  wk
    - check data before updating

    Revision 1.16  2002/11/11 17:58:06  wk
    - show unit

    Revision 1.15  2002/11/07 11:43:01  wk
    - reformat

    Revision 1.14  2002/10/31 17:48:25  wk
    - use buttons

    Revision 1.13  2002/10/24 18:40:59  wk
    - dont show the current date for end date of the project

    Revision 1.12  2002/10/24 14:12:48  wk
    - moved boxes around to have a unique layout

    Revision 1.11  2002/10/22 18:20:47  wk
    - show parent relations for round and close
    - added close

    Revision 1.10  2002/10/22 14:24:57  wk
    - added rounding

    Revision 1.9  2002/10/21 18:27:21  wk
    - replace css-class smooth with disabled

    Revision 1.8  2002/08/26 09:43:27  wk
    - renamed header

    Revision 1.7  2002/08/26 09:08:54  wk
    - added fixed price

    Revision 1.6  2002/08/22 12:42:02  wk
    - added fixedPrice stuff

    Revision 1.5  2002/08/20 16:26:11  wk
    - quite some reformatting, show valid-dates fields too

    Revision 1.4  2002/07/30 20:24:36  wk
    - use CSS-class outline for the table, to show the rows better

    Revision 1.3  2002/07/24 17:06:26  wk
    - some layouting

    Revision 1.2  2002/07/23 14:52:40  wk
    - use tree now

    Revision 1.1.1.1  2002/07/22 09:37:37  wk


-->

{%include vp/Application/HTML/Macro/Tree.mcr%}
{%include vp/Application/HTML/Macro/EditData.mcr%}
{%include common/macro/project.mcr%}
{%include common/macro/common.mcr%}
{%include common/macro/table.mcr%}

<!--
    update
-->
{if( isset($editFolder) )}
    <table>
        <tr>
            <td valign="top">

                <form name="update" method="post" action="{$_SERVER['PHP_SELF']}">
                    <input type="hidden" name="tree[update][id]" value="{$editFolder['id']}">

                    <table class="outline">
                        {%table_headline('edit project','edit')%}

                        <tr>
                            <td>project</td>
                            <td>
                                {$projectTree->getPathAsString($editFolder['id'])}
                            </td>
                        </tr>

                        <tr>
                            <td>new name</td>
                            <td>
                                <input name="tree[update][name]" value="{$editFolder['name']}">
                            </td>
                        </tr>

                        <tr>
                            <td>comment</td>
                            <td>
                                <textarea name="tree[update][comment]" rows="3" cols="30">{$editFolder['comment']}</textarea>
                            </td>
                        </tr>

                        <tr>
                            <td nowrap="nowrap">{%common_help('properties_valid')%} valid</td>
                            <td>
                                {%common_dateInput( 'tree[update][startDate]' , $editFolder['startDate'] , 'update' )%}
                                &nbsp; -
                                {%common_dateInput( 'tree[update][endDate]' , $editFolder['endDate'] , 'update' )%}
                            </td>
                        </tr>

                        <tr>
                            <td nowrap="nowrap">{%common_help('properties_maxDuration')%} max. effort</td>
                            <td>
                                <input size="5" name="tree[update][maxDuration]" value="{$editFolder['maxDuration']}"> hours
                            </td>
                        </tr>

                        <tr>
                            <td nowrap="nowrap">{%common_help('properties_round')%} rounding</td>
                            <td>
                                <input size="3" maxlength="2" name="tree[update][roundTo]" value="{$editFolder['roundTo']}"> minutes
                            </td>
                        </tr>
                                                           
                        {if( $config->hasFeature('price') )}
                            <tr>
                                <td nowrap="nowrap">{%common_help('properties_fixedPrice')%} fixed price</td>
                                <td>
                                    <input name="tree[update][fixedPrice]" value="{$editFolder['fixedPrice']}"> &euro;
                                    <br>
                                    Only needs to be given if this project has a fixed price,<br> if not leave empty.
                                </td>
                            </tr>

                        <tr>
                            <td nowrap="nowrap">{%common_help('properties_close')%} close</td>
                            <td>
                                <input size="3" maxlength="2" name="tree[update][close]" value="{$editFolder['close']}"> days
<!--
                                <br>
                                The number of work days after which the old months data<br> are still editable.
-->
                            </td>
                        </tr>

                        <tr>
                            <td>&nbsp;</td>
                            <td>
                                <input type="submit" name="tree[action][update]" value="save" class="button">
                                <input type="submit" value="Cancel" class="button">
                            </td>
                        </tr>
                    </table>
                </form>

            <td>&nbsp;</td>
                <!--
                    move behind
                -->
            <td valign="top">


                <form name="moveBehind" method="post" action="{$_SERVER['PHP_SELF']}">
                    <table class="outline">
                        {%table_headline( 'move project' , 'move' )%}
                        <tr>
                            <td>Move</td>
                            <td>
                                <input type="hidden" name="tree[move][src_id]" value="{$editFolder['id']}">
                                {$projectTree->getPathAsString($editFolder['id'])}
                            </td>
                        </tr>

                        <tr>
                            <td>under</td>
                            <td>
                                <select name="tree[move][dest_id]">
                                    {%treeAsOptions($allFolders)%}
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td>&nbsp;</td>
                            <td>
                                <input type="submit" name="tree[action][move]" value="save" class="button">
                            </td>
                        </tr>
                    </table>
                </form>
            

            </td>
        </tr>
    </table>



{else}
    <!--
        add new folder
    -->
    <form name="add" method="post" action="{$_SERVER['PHP_SELF']}">
        <table class="outline">
            {%table_headline('add project','add')%}
            <tr>
                <td>
                    name *
                </td>
                <td>
                    <input name="tree[add][name]" class="required">
                </td>
            </tr>

            <tr>
                <td>parent</td>
                <td>
                    <select name="tree[add][parent]">
                        {%Tree_asOptions($allFolders,$selected)%}
                    </select>
                </td>
            </tr>

            <tr>
                <td>comment</td>
                <td>
                    <textarea name="tree[add][comment]" rows="3" cols="30"></textarea>
                </td>
            </tr>

            <tr>
                <td nowrap="nowrap">{%common_help('properties_valid')%} valid</td>
                <td>
                    {%common_dateInput( 'tree[add][startDate]' , time() , 'add' )%}
                    &nbsp; -
                    {%common_dateInput( 'tree[add][endDate]' , null , 'add' )%}
                </td>
            </tr>

            <tr>
                <td nowrap="nowrap">{%common_help('properties_maxDuration')%} max. effort</td>
                <td>
                    <input size="5" name="tree[add][maxDuration]"> hours
                </td>
            </tr>
            
            <tr>
                <td nowrap="nowrap">{%common_help('properties_round')%} rounding</td>
                <td>
                    <input size="3" maxlength="2" name="tree[add][roundTo]"> minutes
                </td>
            </tr>
                                               
            {if( $config->hasFeature('price') )}
                <tr>
                    <td nowrap="nowrap">{%common_help('properties_fixedPrice')%} fixed price</td>
                    <td>
                        <input name="tree[add][fixedPrice]" value="{$editFolder['fixedPrice']}"> &euro;
                        <br>
                        Only needs to be given if this project has a fixed price,<br>if not leave empty.
                    </td>
                </tr>

            <tr>
                <td nowrap="nowrap">{%common_help('properties_close')%} close</td>
                <td>
                    <input size="3" maxlength="2" name="tree[add][close]"> days
<!--                  
                    <br>
                    The number of work days after which the old months data<br> are still editable. 
-->
                </td>
            </tr>

            <tr>
                <td>&nbsp;</td>
                <td>
                    <input type="submit" name="tree[action][add]" value="save" class="button">
                </td>
            </tr>
        </table>
    </form>


<!--
<form name="move" method="post" action="{$_SERVER['PHP_SELF']}">
-->

<br>
<table width="100%" class="outline">
    <tr>
        <th>{%common_help('overview')%} project</th>
        <th>comment</th>
        <th>effort</th>
        <th>start</th>
        <th>end</th>
        <th>round</th> 
        {if( $config->hasFeature('price') )}
            <th>fixed price</th>
        <th>close</th>
        <th>&nbsp;</th>
    </tr>

    {foreach( $allVisibleFolders as $aFolder )}
        { $class = $projectTree->isAvailable($aFolder,time())?'':' class="disabled"'}
        <tr id="removeId{$aFolder['id']}">
            <td valign="top" nowrap {$class}>
                {%project_showNode($aFolder)%}
            </td>

            <td align="left" valign="top" {$class}>
                {echo nl2br($aFolder['comment'])}
            </td>
            
            <td align="right" valign="center" nowrap="nowrap" {$class}>
                {if ($aFolder['maxDuration'])}
                    {$aFolder['maxDuration']} h
                {else}
                    &nbsp;
            </td>

            <td align="left" valign="center" {$class}>
                {$aFolder['startDate']?$dateTime->formatDate($aFolder['startDate']):''}
            </td>
            <td align="left" valign="center" {$class}>
                {$aFolder['endDate']?$dateTime->formatDate($aFolder['endDate']):''}
            </td>

            <td align="right" valign="center" nowrap {$class}>
                {if($aFolder['roundTo'])}
                    <span id="roundParentId_{$aFolder['id']}">{$aFolder['roundTo']} min.</span>
                {else}
                    { $curVal = @$_roundVals[$aFolder['level']-1]['roundTo']}
                    {if( isset($curVal) )}<!-- do only show this if there is a rounding-time given, otherwise it would cause a JS-error on mouseover -->
                        <i>
                        <span onmouseover="bgHighlight('roundParentId_{$_roundVals[$aFolder['level']-1]['id']}')" onmouseout="bgHighlightOff()">
                            {$curVal} min.
                        </span></i>

                <!-- FIXXME do this outside the template  -->
                { $_roundVals[$aFolder['level']] = array('roundTo'=>$aFolder['roundTo']?$aFolder['roundTo']:@$_roundVals[$aFolder['level']-1]['roundTo'],'id'=>$aFolder['roundTo']?$aFolder['id']:@$_roundVals[$aFolder['level']-1]['id'])}
            </td>
                                        
            {if( $config->hasFeature('price') )}
                <td align="right" valign="center" nowrap {$class}>
                    {if($aFolder['fixedPrice'])}
                        {$aFolder['fixedPrice']} &euro;
                </td>

            <td align="right" valign="center" nowrap {$class}>
                {if($aFolder['close'])}
                    <span id="closeParentId_{$aFolder['id']}">{$aFolder['close']} days</span>
                {else}  
                    { $curVal = @$_closeVals[$aFolder['level']-1]['close']}
                    {if( isset($curVal) )}
                        <i>
                        <span onmouseover="bgHighlight('closeParentId_{$_closeVals[$aFolder['level']-1]['id']}')" onmouseout="bgHighlightOff()">
                            {$curVal} days
                        </span></i>

                <!-- FIXXME do this outside the template  -->
                { $_closeVals[$aFolder['level']] = array('close'=>$aFolder['close']?$aFolder['close']:@$_closeVals[$aFolder['level']-1]['close'],'id'=>$aFolder['close']?$aFolder['id']:@$_closeVals[$aFolder['level']-1]['id'])}
            </td>

            <td valign="center" {$class}>
                {%common_editButton($_SERVER['PHP_SELF'].'?id='.$aFolder['id'])%}
            </td>
            <td  valign="center" {$class}>
                {%common_removeAndConfirmButton($_SERVER['PHP_SELF'].'?removeId='.$aFolder['id'] , t('Are you sure you want to delete this project, all his sub projects and all logged times ?') )%}
            </td>

        </tr>
</table>
<!--    under
    <select name="tree[move][dest_id]">
        {%treeAsOptions(&$allFolders)%}
    </select>
    <input type="submit" name="tree[action][move]" value="move">

</form>
-->

{%common_getJS('calendar')%}
{%common_getJS('libs/js/classes/env')%}
{%common_getJS('libs/js/classes/func')%}
<script type="text/javascript" language="JavaScript">

    var highlighted = null;

    function bgHighlight( name )
    \{
        setClass( name , "warning" );
        // set the font weight to normal, so it doesnt change the table
        resolveReference( name ).style.fontWeight = "normal" ;
        // FIXXME doesnt work with the font size :-)
        // resolveReference( name ).style.fontSize = document.body.style.fontSize;
        highlighted = name;
    \}

    function bgHighlightOff()
    \{
        setClass( highlighted , "" );
        highlighted = null;
    \}
</script>
