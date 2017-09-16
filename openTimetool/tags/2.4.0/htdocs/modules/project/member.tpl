<!--

$Id$

-->

{%include common/macro/common.mcr%}
{%include common/macro/user.mcr%}
    
{if( isset($projectId) )}
    <form action="{$_SERVER['PHP_SELF']}" method="post" name="teamEditForm">
        <input type="hidden" name="projectId" value="{$projectId}">
        <table class="outline">
            <thead>
                <tr>
                    <th colspan="3">{$curProject}</th>
                </tr>
                <tr>
                    <td colspan="3">&nbsp;</td>
                </tr>
                <tr>
                    <th>all users</th>
                    <th>&nbsp;</th>
                    <th>project managers</th>
                </tr>
            </thead>

            <tfoot>
                <tr>
                    <td colspan="3" align="center">
                        <input type="Submit" name="action_save" value="Save" class="button" onclick="selectRelevant()">
                        <input type="button" value="Cancel" onclick="window.location='{$_SERVER['PHP_SELF']}'" class="button">
                    </td>
                </tr>
            </tfoot>

            <tbody>
                <tr>
                    <td rowspan="4">
                        <select size="{$selectSizeUsers}" multiple style="width:200px;" name="allUsers[]">
                            {%usersAsOptions($users)%}
                        </select>
                    </td>

                    <td align="center" valign="middle" nowrap="nowrap">
                        <span class="button" style="padding:3px;">
                            <a href="javascript://" onclick="user2Manager();">
                                <img src="arrowRight.gif" width="16" height="16" alt="right"></a>
                        </span>
                        <br><br>
                        <span class="button" style="padding:3px;">
                            <a href="javascript://" onclick="manager2User();">
                                <img src="arrowLeft.gif" width="16" height="16" alt="left"></a>
                        </span>
                        <br>
                    </td>
                    <td align="center">
                        <select size="{$selectSizeManagers}" multiple style="width:200px;" name="managers[]">
                            {%usersAsOptions($managers)%}
                        </select>
                    </td>
                </tr>

                <tr>
                    <td nowrap="nowrap"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </td>
                    <td align="center">
                        <br>
                        <span class="button" style="padding:3px;">
                            <a href="javascript://" onclick="member2Manager();">
                                <img src="arrowUp.gif" width="16" height="16" alt="up"></a>
                        </span>
                        &nbsp;
                        <span class="button" style="padding:3px;">
                            <a href="javascript://" onclick="manager2Member();">
                                <img src="arrowDown.gif" width="16" height="16" alt="down"></a>
                        </span>
                        <br><br>
                    </td>
                </tr>

                <tr>
                    <td align="center" valign="middle" rowspan="2">
                        <span class="button" style="padding:3px;">
                            <a href="javascript://" onclick="user2Member();">
                                <img src="arrowRight.gif" width="16" height="16" alt="right"></a>
                        </span>
                        <br><br>
                        <span class="button" style="padding:3px;">
                            <a href="javascript://" onclick="member2User();">
                                <img src="arrowLeft.gif" width="16" height="16" alt="left"></a>
                        </span>
                        <br>
                    </td>

                    <th>team members</th>

                </tr>
                <tr>
                    <td align="center">
                        <select size="{$selectSizeMembers}" multiple style="width:200px;" name="members[]">
                            {%usersAsOptions($members)%}
                        </select>
                    </td>
                </tr>

                <tr>
                    <td colspan="3">
                        <input type="checkbox" name="InheritTeam" value="1" style="vertical-align:middle;">
                        Inherit team from parent project '{$parentProject}'
                    </td>                
                </tr>
            </tbody>
        </table>
    </form>

{else}
    {%common_getJS($projectTreeJsFile,true)%}
    <script>
        projectTree.init(false);
    </script>

{%common_getJS('/libs/js/classes/form')%}    
<script>
    function getSelectedIds(selectBox)
    \{
        allOptions = document.teamEditForm[selectBox].options;
        indexes = new Array();
        for (i=0;i<allOptions.length;i++) \{
            if (document.teamEditForm[selectBox][i].selected) \{
                indexes[indexes.length] = i;
            \}
        \}
        return indexes;
    \}

    function member2User()
    \{
        select2select("members[]","allUsers[]");
    \}

    function manager2User()
    \{
        select2select("managers[]","allUsers[]");
    \}

    function member2Manager()
    \{
        select2select("members[]","managers[]");
    \}

    function manager2Member()
    \{
        select2select("managers[]","members[]");
    \}

    function user2Manager()
    \{
        select2select("allUsers[]","managers[]");
    \}

    function user2Member()
    \{
        select2select("allUsers[]","members[]");
    \}

    formClass = new class_form(document.teamEditForm);
    function select2select(from,to) 
    \{
        formClass.select2select(from,to,true);
    \}

    /**
    *   this function selects the relevant entries in managers and members
    *   to submit them, if they are not selected, they dont get submitted :-(
    */
    function selectRelevant()
    \{
        formClass.selectAll("managers[]");
        formClass.selectAll("members[]");
    \}
</script>
