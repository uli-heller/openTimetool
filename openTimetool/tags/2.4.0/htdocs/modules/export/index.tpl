<!--

$Id$

-->

{%include vp/Application/HTML/Macro/NextPrev.mcr%}
{%include common/macro/common.mcr%}

<center>
    <form method="post" name="exportForm">
    {if( $exports )}
        <table class="outline">
            <thead>
                <tr>
                    <th>source</th>
                    <th>destination format</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>

            <tfoot>
                <tr>
                    <td colspan="3" align="center">
                        <input type="button" value="next &gt;&gt;" onclick="gotoExportPage()" class="button">
                    </td>
                </tr>
            </tfoot>

            <tbody>
            {foreach( $exports as $source=>$aExport )}
                <tr>
                    <td rowspan="{echo sizeof($aExport)}">
                        {$source}
                    </td>
                    <td>
                        {$aExport[0]['type']}
                    </td>
                    <td>
                        <input type="radio" name="exportType" value="{$aExport[0]['file']}" onclick="gotoExportPage()">
                    </td>
                </tr>
                { array_shift($aExport)}
                {foreach( $aExport as $aDest)}
                    <tr>
                        <td>
                            <!-- added some issets : AK -->
                            {if(isset($aDest['todo']))}
                                <span class="disabled">{$aDest['type']}</span>
                            {else}
                                {$aDest['type']}
                        </td>
                        <td>
                            {if(!isset($aDest['todo']))}
                                <input type="radio" name="exportType" value="{$aDest['file']}" onclick="gotoExportPage()">
                        </td>
                    </tr>
            </tbody>
        </table>
    </form>

    <br>

    {if( sizeof($exportedFiles) )}
        <table class="outline" width="95%">                       
            <!-- only when i send the data via 'get' it works in IE :-( i have no clue why but that's how it works -->
            <form action="download.php" method="get" name="downloadForm" target="_blank">
            <input type="hidden" name="id" value="">

            <thead>
                <tr>
                    <th colspan="6">last exports</th>
                </tr>
                <tr>
                    <td colspan="6"><br></td>
                </tr>
                <tr>
                    <th>date</th>
                    <th>project(s)</th>
                    <th>template</th>
                    <th>download</th>
                    <th>open</th>
                    <th>delete</th>
                </tr>
            </thead>

            <tfoot>
                <tr>
                    <td align="center" colspan="7">
                        <form method="post" action="{$_SERVER['PHP_SELF']}">
                            {%NextPrev_Buttons($nextPrev)%}
                        </form>
                    </td>
                </tr>
            </tfoot>

            <tbody>
            <!-- added @ to avoid notices -->
            {foreach( $exportedFiles as $aFile )}
                <tr id="removeId{$aFile['id']}" {$aFile['id']==@$_REQUEST['exportedId']?'class="success"':'' }>
                    <td>
                        {$dateTime->format($aFile['timestamp'])}
                    </td>
                    <td>
                        {foreach( $aFile['projects'] as $aProject)}
                            {$aProject}
                            <br>
                    </td>
                    <td>
                        {$aFile['_OOoTemplate_name']}
                    </td>
                    <td align="center">
                        <a href="download.php?id={$aFile['id']}&download=true">
                            <img src="{$config->vImgRoot}/fileIcons/{$aFile['type']}.gif" alt="{$aFile['_type']}" title="{$aFile['_type']}"></a>
                    </td>
                    <td>
                        <input type="submit" onclick="this.form.id.value={$aFile['id']}" value=" &gt; " class="button">
<!--
                        <input type="radio" name="id" value="{$aFile['id']}">
-->
                    </td>
                    <td valign="middle">
                        {%common_removeAndConfirmButton($_SERVER['PHP_SELF'].'?removeId='.$aFile['id'] , t('Are you sure you want to delete this report?') )%}
                    </td>
                </tr>
            {if($isAdmin)}
                <tr id="all">
                    <td colspan="5" align="right">
                        delete all exports
                    </td>
                    <td valign="middle">
                        {%common_removeAndConfirmButtonAll($_SERVER['PHP_SELF'].'?removeId=all' , t('Are you sure you want to delete all exports?') )%}
                    <!--
                        {%common_removeAndConfirmButton($_SERVER['PHP_SELF'].'?removeId='.$aFile['id'] , t('Are you sure you want to delete this report?') )%}
                    -->
                    </td>
            	</tr>
            </tbody>
            </form>
        </table>
</center>

<script>
    // automatically download if a exportedId is given, that is when
    // someone comes from another page to download a file here
    // 
    // AK : added isset
    {if( isset($_REQUEST['exportedId']) )}
        document.downloadForm.id.value = {$_REQUEST['exportedId']};
        document.downloadForm.submit();

    function gotoExportPage()
    \{
        type = document.exportForm.exportType
        for (i=0 ; i<type.length ; i++)
        \{
            if (type[i].checked)
            \{
                window.location = type[i].value;
                return;
            \}
        \}
    \}
</script>
