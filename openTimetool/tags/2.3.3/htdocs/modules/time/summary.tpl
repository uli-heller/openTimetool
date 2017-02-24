<!--
    $Log: summary.tpl,v $
    Revision 1.6  2002/10/22 14:42:17  wk
    - replace class smooth by disabled

    Revision 1.5  2002/08/26 09:09:12  wk
    - added currency

    Revision 1.4  2002/08/22 12:42:29  wk
    - added fixedPrice calc

    Revision 1.3  2002/08/21 20:22:46  wk
    - _hacked_ the prices

    Revision 1.2  2002/08/20 16:29:54  wk
    - show data properly

    Revision 1.1  2002/08/20 09:02:28  wk
    - initial commit

-->

{%include common/macro/project.mcr%}

{ $grandTotalTime=0}
{ $grandTotalExternal=0}
{ $grandTotalInternal=0}

<table class="outline">
    <tr>
        <td colspan="5">
            * the project was offered with a fixed price, that is shown here.
            <br>
            ** virtual profit, if the project would have been offered on an hourly basis
        </td>
    </tr>

    <tr>
        <th>project</th>
        <th>task</th>
        <th>internal</th>
        <th>external</th>
        <th>time</th>
    </tr>

    {foreach( $projects as $key=>$aProject )}
        { $sumTime=0}
        { $sumInternal=0}
        { $sumExternal=0}
        {foreach( $timesKeys[$aProject['id']] as $i=>$aTimeKey )}
            { $aTime=$times[$aTimeKey]}
            { $sumTime +=$aTime['totalTime']}
            { $sumInternal +=$aTime['totalInternal']}
            { $sumExternal +=$aTime['totalExternal']}
            <tr>
                {if( $i != 0 )}
                    <td>&nbsp;</td>
                {else}
                    <td valign="top" nowrap {$class}>
                        {%project_showNode($aProject)%}
                    </td>
                <td>{$aTime['task']}</td>
                <td align="right">{$aTime['totalInternal']} &euro;</td>
                <td align="right">{$aTime['totalExternal']} &euro;</td>
                <td align="right">
                    {printf('%.2f',$aTime['totalTime']/(60*60))} h
                </td>
            </tr>
        {else}
            <tr>
                <td colspan="5" valign="top" nowrap {$class}>
                    {%project_showNode($aProject)%}
                </td>
            </tr>

        <!--
            the summary for one project
        -->
        {if( $sumTime )}
            <tr>
                <td colspan="2" align="right" valign="bottom">
                    <b><i>Profit:</i></b>
                </td>
                <td align="right" valign="top">
                    <b>{$util->formatPrice($sumInternal)} &euro;</b>
                </td>
                <!--
                    external price handling, handle if a fixed price is given
                -->
                <td align="right" valign="top">
                    {if($aProject['fixedPrice'])}
                        <font class="disabled">
                            {$util->formatPrice($sumExternal)} &euro;<br>
                            ** {$util->formatPrice($sumExternal-$sumInternal)} &euro;<br>
                        </font>
                        * <b>{$util->formatPrice($aProject['fixedPrice'])} &euro;</b><br>
                        { $sumExternal=$aProject['fixedPrice']}
                    {else}
                        <b>{$util->formatPrice($sumExternal)} &euro;</b><br>

                    <!-- this is the profit -->
                    <b><i>{$util->formatPrice($sumExternal-$sumInternal)} &euro;</i></b>
                    { $grandTotalExternal += $sumExternal}
                    { $grandTotalInternal += $sumInternal}
                </td>

                <!--
                    sum hours
                -->
                <td align="right" valign="top">
                    <b>
                        {printf('%.2f',$sumTime/(60*60))} h<br>
                        {printf('%.2f',$sumTime/(60*60*8))} d
                    </b>
                    { $grandTotalTime += $sumTime}
                </td>
            </tr>

    <tr>
        <td colspan="2" align="right" valign="bottom">
            <font class="success">Profit:</font>
        </td>
        <td align="right" valign="top">
            <b>{$util->formatPrice($grandTotalInternal)} &euro;</b>
        </td>
        <!--
            external price handling, handle if a fixed price is given
        -->
        <td align="right" valign="top">
            {$util->formatPrice($grandTotalExternal)} &euro;<br>
            <font class="success">{$util->formatPrice(($grandTotalExternal-$grandTotalInternal))} &euro;</font>
        </td>

        <td align="right" valign="top">
            <b>
                {printf('%.2f',$grandTotalTime/(60*60))} h<br>
                {printf('%.2f',$grandTotalTime/(60*60*8))} d
            </b>
        </td>
    </tr>
</table>
