<!--

$Id$

-->   

{%include vp/Application/HTML/Macro/EditData.mcr%}

<center>
    <form action="{$_SERVER['PHP_SELF']}" method="post" enctype="multipart/form-data" name="templateForm">
        <table class="outline">
            <thead>
                <tr>
                    <th colspan="2">OpenOffice.org* - Templates</th>
                </tr>
            </thead>

            <tfoot>
                <tr>
                    <td colspan="2" align="center">
                        <input type="submit" value="export" name="export" onclick="this.form.saveTemplate.value=1" class="button">
                    </td>
                </tr>
            </tfoot>

            <tbody>
                <tr>
                    <td colspan="2" align="center" style="padding:10px;">
                        <table class="outline">
                        {if( $templates )}
                            <thead>
                                <tr>
                                    <th>name</th>
                                    <th>date</th>
                                    <th>type</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>

                            <tfoot>
                                <tr>
                                    <td colspan="4" align="center">
                                        <input type="submit" value="export" name="export" class="button">
                                    </td>
                                </tr>
                            </tfoot>

                            <tbody>
                            {foreach( $templates as $aTemplate )}
                                <tr>
                                    <td>{$aTemplate['name']}</td>
                                    <td align="center">{echo date('d.m.y H:i',$aTemplate['timestamp'])}</td>
                                    <td>{$aTemplate['_type']}</td>
                                    <td>
                                        <input type="radio" name="template_id" value="{$aTemplate['id']}" onclick="this.form.submit()">
                                    </td>
                                </tr>
                            </tbody>
                        {else}
                            <tr>
                                <td align="center">
                                    <br>
                                    No templates uploaded yet!
                                    <br><br>
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
                </tr>     for now we dont handle templates that wont be saved!!!
-->             <input type="hidden" name="saveTemplate" value="0">
            </tbody>
        </table>
    </form>

    <br>

    <table class="outline">
        <thead>
            <tr>
                <th colspan="2">export data</th>
            </tr>
        </thead>

        <tbody>
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
        </tbody>
    </table>

    <br><br>

    * If you don't have the OpenOffice-Suite installed on your system,<br>
    you can download it for free from <a href="http://www.openoffice.org" target="_blank">www.openoffice.org</a>.
</center>
