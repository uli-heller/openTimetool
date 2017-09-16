<!--

$Id$

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
                        <thead>
                            {%table_headline('edit project','edit')%}
                        </thead>

                        <tfoot>
                            <tr>
                                <td>&nbsp;</td>
                                <td>
                                    <input type="submit" name="tree[action][update]" value="save" class="button">
                                    <input type="submit" value="Cancel" class="button">
                                </td>
                            </tr>
                        </tfoot>

                        <tbody>
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
                        </tbody>
                    </table>
                </form>

            <td>&nbsp;</td>
            <!--
                move behind
            -->
            <td valign="top">
                <form name="moveBehind" method="post" action="{$_SERVER['PHP_SELF']}">
                    <table class="outline">
                        <thead>
                            {%table_headline( 'move project' , 'move' )%}
                        </thead>

                        <tfoot>
                            <tr>
                                <td>&nbsp;</td>
                                <td>
                                    <input type="submit" name="tree[action][move]" value="save" class="button">
                                </td>
                            </tr>
                        </tfoot>

                        <tbody>
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
                        </tbody>
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
            <thead>
                {%table_headline('add project','add')%}
            </thead>

            <tfoot>
                <tr>
                    <td>&nbsp;</td>
                    <td>
                        <input type="submit" name="tree[action][add]" value="save" class="button">
                    </td>
                </tr>
            </tfoot>

            <tbody>
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
            </tbody>
        </table>
    </form>


<!--
<form name="move" method="post" action="{$_SERVER['PHP_SELF']}">
-->

<br>
<table width="100%" class="outline">
    <thead>
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
    </thead>

    <tbody>
    {foreach( $allVisibleFolders as $aFolder )}
        { $class = $projectTree->isAvailable($aFolder,time())?'':' class="disabled"'}
        <tr id="removeId{$aFolder['id']}">
            <td valign="top" nowrap="nowrap" {$class}>
                {%project_showNode($aFolder)%}
            </td>

            <td align="left" valign="top" {$class}>
                {echo nl2br($aFolder['comment'])}
            </td>
            
            <td align="right" valign="middle" nowrap="nowrap" {$class}>
                {if ($aFolder['maxDuration'])}
                    {$aFolder['maxDuration']} h
                {else}
                    &nbsp;
            </td>

            <td align="left" valign="middle" {$class}>
                {$aFolder['startDate']?$dateTime->formatDate($aFolder['startDate']):''}
            </td>
            <td align="left" valign="middle" {$class}>
                {$aFolder['endDate']?$dateTime->formatDate($aFolder['endDate']):''}
            </td>

            <td align="right" valign="middle" nowrap="nowrap" {$class}>
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
                <td align="right" valign="middle" nowrap="nowrap" {$class}>
                    {if($aFolder['fixedPrice'])}
                        {$aFolder['fixedPrice']} &euro;
                </td>

            <td align="right" valign="middle" nowrap="nowrap" {$class}>
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

            <td align="center" valign="middle" {$class}>
                {%common_editButton($_SERVER['PHP_SELF'].'?id='.$aFolder['id'])%}
            </td>
            <td align="center" valign="middle" {$class}>
                {if( $projectTree->getRootId() == $aFolder['id'] )}
                    <div style="width:26px;height:17px;"> </div>
                {else}
                    {%common_removeAndConfirmButton($_SERVER['PHP_SELF'].'?removeId='.$aFolder['id'] , t('Are you sure you want to delete this project, all its sub projects and all logged times ?\n\nAttention! All data of these projects will be irrevocably deleted!') )%}
            </td>

        </tr>
    </tbody>
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

<script>
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
