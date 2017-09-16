<!--

$Id$

-->

{%include common/macro/project.mcr%}

{ $grandTotalTime=0}
{ $grandTotalExternal=0}
{ $grandTotalInternal=0}

<table class="outline">
    <thead>
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
    </thead>

    <tbody>
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
                        <td valign="top" nowrap="nowrap" {$class}>
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
                    <td colspan="5" valign="top" nowrap="nowrap" {$class}>
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
                            <span class="disabled">
                                {$util->formatPrice($sumExternal)} &euro;<br>
                                ** {$util->formatPrice($sumExternal-$sumInternal)} &euro;<br>
                            </span>
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
                <span class="success">Profit:</span>
            </td>
            <td align="right" valign="top">
                <b>{$util->formatPrice($grandTotalInternal)} &euro;</b>
            </td>
            <!--
                external price handling, handle if a fixed price is given
            -->
            <td align="right" valign="top">
                {$util->formatPrice($grandTotalExternal)} &euro;<br>
                <span class="success">{$util->formatPrice(($grandTotalExternal-$grandTotalInternal))} &euro;</span>
            </td>

            <td align="right" valign="top">
                <b>
                    {printf('%.2f',$grandTotalTime/(60*60))} h<br>
                    {printf('%.2f',$grandTotalTime/(60*60*8))} d
                </b>
            </td>
        </tr>
    </tbody>
</table>
