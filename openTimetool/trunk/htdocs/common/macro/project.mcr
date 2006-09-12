<!--
   $Log: project.mcr,v $
   Revision 1.12.2.1  2003/03/19 19:39:16  wk
   - make the multiSelect stuff work

   Revision 1.12  2003/03/04 19:12:12  wk
   - working on the multiSelect

   Revision 1.11  2003/02/17 19:16:01  wk
   - save multiple preselects too

   Revision 1.10  2003/02/13 16:15:55  wk
   - nothing serious

   Revision 1.9  2003/02/10 19:12:38  wk
   - removed allFolders parameter, since it is generated in JS

   Revision 1.8  2003/02/10 16:18:14  wk
   - set global JS var for the tree to preselect a project

   Revision 1.7  2002/12/11 12:24:30  wk
   - add space before the folder names

   Revision 1.6  2002/11/07 11:42:11  wk
   - some beautifying

   Revision 1.5  2002/10/31 17:43:46  wk
   - show unfold icon

   Revision 1.4  2002/10/22 14:24:13  wk
   - moved macros here, since common is included in a file where the macro-files used are not included

   Revision 1.3  2002/08/22 12:41:42  wk
   - show unfoldAll link

   Revision 1.2  2002/08/20 16:24:16  wk
   - added the macro showNode, which shows the folder state, etc.

   Revision 1.1.1.1  2002/07/22 09:37:37  wk


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
                <img src="common/openFolder" border="0" style="vertical-align:bottom;"></a>
            {else}
                <img src="common/closedFolder" border="0" style="vertical-align:bottom;"></a>
    {else}
        <img src="common/folder" style="vertical-align:bottom;">
    
    &nbsp;
    <b>{$aProject['name']}</b>

    {if( $projectTree->getRootId() == $aProject['id'] )}
        &nbsp;
        <a href="{$_SERVER['PHP_SELF']}?unfoldAll=true" title="unfold all">
            <img src="viewTree.gif" border="0" style="vertical-align:bottom;"></a>
                               
        
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
                <select name="{$name}" multiple style="position:absolute; visibility:hidden;"></select>
                <div id="projectText{$_projectTextCounter}" class="treeMenuMultiText" onmousedown="projectTree.show();" style="width:300px; border:1px white outset;"></div>
            {else}
                <input type="hidden" name="{$name}"/>
                <input type="button" id="projectText{$_projectTextCounter}" onclick="projectTree.show();" value="...select project..." class="button"/>
            <script language="JavaScript">
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

