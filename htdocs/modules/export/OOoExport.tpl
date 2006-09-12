<!--
    $Log: OOoExport.tpl,v $
    Revision 1.9  2002/12/09 13:50:25  wk
    - added some required classes and button

    Revision 1.8  2002/12/09 12:20:24  wk
    - added class=button for IE

    Revision 1.7  2002/12/02 09:49:15  wk
    - the updated I18N takes care of translating this properly too :-)

    Revision 1.6  2002/12/01 13:33:21  wk
    - make the text translateable

    Revision 1.5  2002/11/30 19:08:17  wk
    - changed some text

    Revision 1.4  2002/11/20 20:09:54  wk
    - let only admins upload templates and show only user's exported files

    Revision 1.3  2002/11/12 15:29:48  wk
    - auto submit on click

    Revision 1.2  2002/11/12 13:11:08  wk
    - show export info

    Revision 1.1  2002/11/11 17:56:56  wk
    - initial commit

-->    

{%include vp/Application/HTML/Macro/EditData.mcr%}

<center>
           
    <form action="{$_SERVER['PHP_SELF']}" method="POST" enctype="multipart/form-data" name="templateForm">
        <table class="outline">
            <tr>
                <th colspan="2">OpenOffice.org* - Templates</th>
            </tr>

            <tr>
                <td colspan="2" align="center" style="padding:10px;">
                    <table class="outline">
                        {if( $templates )}
                            <tr>
                                <th>name</th>
                                <th>date</th>
                                <th>type</th>
                                <th>&nbsp;</th>
                            </tr>
                        {foreach( $templates as $aTemplate )}
                            <tr>
                                <td>
                                    <a href="downloadTemplate.php?id={$aTemplate['id']}">{$aTemplate['name']}</a>
                                </td>
                                <td align="center">{echo date('d.m.y H:i',$aTemplate['timestamp'])}</td>
                                <td>{$aTemplate['_type']}</td>
                                <td>
                                    <input type="radio" name="template_id" value="{$aTemplate['id']}" onClick="this.form.submit()">
                                </td>
                            </tr>
                        {else}
                            <tr>
                                <td align="center">
                                    <br>
                                    No templates uploaded yet!
                                    <br><br>
                                </td>
                            </tr>
                        {if( $templates )}
                            <tr>
                                <td colspan="4" align="center">
                                    <input type="submit" value="export" name="export" class="button">
                                </td>
                            </tr>
                    </table>
                </td>
            </tr>


            <tr>                
                {if( $isAdmin )}
                    <th colspan="2">new template (upload and store)</th>
                {else}
                    <th colspan="2">use template (upload)</th>
            </tr>

            {if($isAdmin)}
                {%EditData_input( $data , 'name' , t('name').' *')%}
            {%EditData_inputFile( $data , 'file' , t('file').' *')%}

<!--
            <tr>
                <td>save template</td>
                <td>
                    <input type="checkbox" name="saveTemplate">
                </td>
            </tr>                    for now we dont handle templates that wont be saved!!!
-->         <input type="hidden" name="saveTemplate" value="0">

            <tr>
                <td colspan="2" align="center">
                    <input type="submit" value="export" name="export" onClick="this.form.saveTemplate.value=1" class="button"/>
                </td>
            </tr>
        </table>
    </form>

    <br>
    
    <table class="outline">
        <tr>
            <th colspan="2">export data</th>
        </tr>

        <tr>
            <td>time</td>
            <td>{$show['humanDateFrom']} until {$show['humanDateUntil']}</td>
        </tr>

        <tr>
            <td>projects</td>
            <td>
                {foreach( $projects as $aProject )}
                    {$aProject}
                    <br>
            </td>
        </tr>
    </table>

    <br><br>

    * If you don't have the OpenOffice-Suite installed on your system,<br>
    you can download it for free from <a href="http://www.openoffice.org" target="_blank">www.openoffice.org</a>.
</center>
