<!--
    $Log: member.tpl,v $
    Revision 1.7  2003/03/04 19:15:39  wk
    - editing members is now fully JS

    Revision 1.6  2003/02/10 19:14:21  wk
    - use projectTreeDyn now

    Revision 1.5  2002/12/13 10:07:48  wk
    - just a little cosmetic

    Revision 1.4  2002/12/09 13:50:25  wk
    - added some required classes and button

    Revision 1.3  2002/11/13 19:01:16  wk
    - comment in a comment :-)

    Revision 1.2  2002/10/31 17:48:25  wk
    - use buttons

    Revision 1.1  2002/10/28 11:19:43  wk
    - for editing project members

-->

{%include common/macro/common.mcr%}
{%include common/macro/user.mcr%}
    
{if( isset($projectId) )}
    <form action="{$_SERVER['PHP_SELF']}" method="post" name="teamEditForm">
        <input type="hidden" name="projectId" value="{$projectId}">
        <table class="outline">
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

            <tr>
                <td rowspan="4">
                    <select size="{$selectSizeUsers}" multiple style="width:200px;" name="allUsers[]">
                        {%usersAsOptions($users)%}
                    </select>
                </td>

                <td align="center" valign="center" nowrap="nowrap">
                    <span class="button" style="padding:3px;">
                        <a href="javascript://" onClick="user2Manager();">
                            <img src="arrowRight" border="0"/></a>
                    </span>
                    <br><br>
                    <span class="button" style="padding:3px;">
                        <a href="javascript://" onClick="manager2User();">
                            <img src="arrowLeft" border="0"/></a>
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
                        <a href="javascript://" onClick="member2Manager();">
                            <img src="arrowUp" border="0"/></a>
                    </span>
                    &nbsp;
                    <span class="button" style="padding:3px;">
                        <a href="javascript://" onClick="manager2Member();">
                            <img src="arrowDown" border="0"/></a>
                    </span>
                    <br><br>
                </td>
            </tr>
            
            <tr>

                <td align="center" valign="center" rowspan="2">
                    <span class="button" style="padding:3px;">
                        <a href="javascript://" onClick="user2Member();">
                            <img src="arrowRight" border="0"/></a>
                    </span>
                    <br><br>
                    <span class="button" style="padding:3px;">
                        <a href="javascript://" onClick="member2User();">
                            <img src="arrowLeft" border="0"/></a>
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
                <td colspan="3" align="center">
                    <input type="Submit" name="action_save" value="Save" class="button" onClick="selectRelevant()"/>
                    <input type="button" value="Cancel" onClick="window.location='{$_SERVER['PHP_SELF']}'" class="button"/>
                </td>
            </tr>
        </table>
    </form>

{else}
    {%common_getJS($projectTreeJsFile,true)%}
    <script type="text/javascript" language="JavaScript">
        projectTree.init(false);
    </script>


    
{%common_getJS('/libs/js/classes/form')%}    
<script>
    function getSelectedIds(selectBox)
    \{
        allOptions = document.teamEditForm[selectBox].options;
        indexes = new Array();
        for(i=0;i<allOptions.length;i++) \{
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

