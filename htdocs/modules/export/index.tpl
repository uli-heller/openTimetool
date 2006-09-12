<!--
	$Id
	
	
	************* switch to svn *******
    $Log: index.tpl,v $
    Revision 1.12  2003/02/14 15:40:27  wk
    - extension is set in the php file

    Revision 1.11  2003/01/29 10:32:09  wk
    - set table width to have unique apperance bug 0000048

    Revision 1.10  2002/12/09 12:20:24  wk
    - added class=button for IE

    Revision 1.9  2002/12/05 14:19:18  wk
    - show a download icon

    Revision 1.8  2002/11/30 19:08:17  wk
    - changed some text

    Revision 1.7  2002/11/25 10:48:30  wk
    - add next-prev logic for exported files

    Revision 1.6  2002/11/12 18:00:12  wk
    - auto download files

    Revision 1.5  2002/11/12 15:30:29  wk
    - auto submit on click

    Revision 1.4  2002/11/12 13:12:13  wk
    - added headline to donload-table

    Revision 1.3  2002/11/11 17:57:44  wk
    - index does different thing now!

    Revision 1.2  2002/08/05 19:24:21  wk
    - added some more documentation

    Revision 1.1  2002/08/05 18:52:26  wk
    - initial commit

-->

{%include vp/Application/HTML/Macro/NextPrev.mcr%}

<center>
    <form method="post" name="exportForm">
    {if( $exports )}
        <table class="outline">
            <tr>
                <th>source</th>
                <th>destination format</th>
                <th>&nbsp;</th>
            </tr>

            {foreach( $exports as $source=>$aExport )}
                <tr>
                    <td rowspan="{echo sizeof($aExport)}">
                        {$source}
                    </td>
                    <td>
                        {$aExport[0]['type']}
                    </td>
                    <td>
                        <input type="radio" name="exportType" value="{$aExport[0]['file']}" onClick="gotoExportPage()">
                    </td>
                </tr>
                { array_shift($aExport)}
                {foreach( $aExport as $aDest)}
                    <tr>
                        <td>
                        	<!-- added some issets : AK -->
                            {if(isset($aDest['todo']))}
                                <font class="disabled">{$aDest['type']}</font>
                            {else}
                                {$aDest['type']}
                        </td>
                        <td>
                            {if(!isset($aDest['todo']))}
                                <input type="radio" name="exportType" value="{$aDest['file']}" onClick="gotoExportPage()">
                        </td>
                    </tr>

            <tr>
                <td colspan="3" align="center">
                    <input type="button" value="next &gt;&gt;" onClick="gotoExportPage()" class="button">
                </td>
            </tr>

        </table>
    </form>

    <br>
                     
    {if( sizeof($exportedFiles) )}
        <table class="outline" width="95%">                       
            <!-- only when i send the data via 'get' it works in IE :-( i have no clue why but that's how it works -->
            <form action="download.php" method="get" name="downloadForm" target="_blank">
            <input type="hidden" name="id" value="">

            <tr>
                <th colspan="5">last exports</th>
            </tr>

            <tr>
                <td colspan="5"><br></td>
            </tr>

            <tr>
                <th>date</th>
                <th>project(s)</th>
                <th>template</th>
                <th>download</th>
                <th>open</th>
            </tr>
			<!-- added @ to avoid notices -->
            {foreach( $exportedFiles as $aFile )}
                <tr {$aFile['id']==@$_REQUEST['exportedId']?'class="success"':''}>
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
                            <img src="{$config->vImgRoot}/fileIcons/{$aFile['type']}.gif" alt="{$aFile['_type']}" title="{$aFile['_type']}" border="0"></a>
                    </td>
                    <td>
                        <input type="submit" onClick="this.form.id.value={$aFile['id']}" value=" &gt; " class="button">
<!--
                        <input type="radio" name="id" value="{$aFile['id']}">
-->
                    </td>
                </tr>
<!--
            <tr>
                <td colspan="5" align="center">
                    <input type="submit" value="download again">
                </td>
            </tr>
-->
            </form>
            
            <tr>
                <td align="center" colspan="7">
                    <form method="post" action="{$_SERVER['PHP_SELF']}">
                        {%NextPrev_Buttons($nextPrev)%}
                    </form>
                </td>
            </tr>
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
        for( i=0 ; i<type.length ; i++ )
        \{                              
            if( type[i].checked )                      
            \{
                window.location = type[i].value;
                return;
            \}
        \}
    \}
</script>
