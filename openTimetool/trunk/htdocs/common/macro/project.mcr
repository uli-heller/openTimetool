<!--

$Id$

-->

<!--
    this macro shows a node for the explorer view

    @param  array   the current project
-->
{%macro project_showNode($aProject)%}
    {global $projectTree,$session}
    {%repeat $aProject['level'] times%}
        &nbsp; &nbsp;

    {if( $projectTree->hasChildren($aProject['id']) )}
        <a name="projectTreeId_{$aProject['id']}"></a>

        <a href="{$_SERVER['PHP_SELF']}?unfold={$aProject['id']}">
            {if(isset($session->temp->openProjectFolders[$aProject['id']]))}
                <img src="common/openFolder.gif" alt="open" width="31" height="16" style="vertical-align:bottom;"></a>
            {else}
                <img src="common/closedFolder.gif" alt="close" width="31" height="16" style="vertical-align:bottom;"></a>
    {else}
        <img src="common/folder.gif" alt="folder" width="31" height="16" style="vertical-align:bottom;">
    
    &nbsp;
    <b>{$aProject['name']}</b>

    {if( $projectTree->getRootId() == $aProject['id'] )}
        &nbsp;
        <!-- a href="{$_SERVER['PHP_SELF']}?unfoldAll=true" title="unfold all">
            <img src="common/viewTree.gif" alt="tree" width="16" height="17" style="vertical-align:bottom;"></a -->
        
<!--
    show a table-row with a drop down which lets the user choose a project

    @param  array   the result of $projectTree->getAll()
    @param  int     the id of the selected project
    @param  string  the name used for the select box
-->
{%macro project_row($selectedProject=0,$name='newData[projectTree_id]')%}

    {global $_projectTextCounter}

    {if(!isset($_projectTextCounter))}
        { $_projectTextCounter = "";}   <!-- empty to let the default name be "projectText" -->
    {else}
        {if ($_projectTextCounter == "")}
            { $_projectTextCounter = 0;}
        {else}
            { $_projectTextCounter++;}

    <tr>
        <td>Project</td>
        <td valign="middle">
            {if (substr($name,-2)=='[]')}
                <select name="{$name}" multiple style="position:absolute;visibility:hidden;"></select>
                <div id="projectText{$_projectTextCounter}" class="treeMenuMultiText" onmousedown="projectTree.show();" style="width:300px;border:1px white outset;"></div>
            {else}
                <input type="hidden" name="{$name}">
                <input type="button" id="projectText{$_projectTextCounter}" onclick="projectTree.show();" value="...select project..." class="button">
            <script>
                <!-- at the time when this js is executed the projectTree object doesnt exist yet
                    so we tell it to read this global var!
                -->
                var _projectTree_preMultiSelect = null;
                var _projectTree_preSelect = null;
                {if (substr($name,-2)=='[]')}
                    _projectTree_preMultiSelect = new Array();
                    {if (is_array($selectedProject) && sizeof($selectedProject)>0)}
                        {foreach ($selectedProject as $key=>$aProject)}
                            _projectTree_preMultiSelect[{$key}] = {$aProject};
                {else}
                    _projectTree_preSelect = {$selectedProject?$selectedProject:0};
            </script>
        </td>
    </tr>
