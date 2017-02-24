<!--
    $Log: project.tpl,v $
    Revision 1.2.2.3  2003/03/27 16:04:41  wk
    - show expired projects using the disabled color

    Revision 1.2.2.2  2003/03/27 15:57:27  wk
    - well, we want to see the percent when there is a maxDuration ... geee

    Revision 1.2.2.1  2003/03/27 15:55:55  wk
    - if there is a duration given, no matter how small, we also want to see the percentage

    Revision 1.2  2003/02/18 20:30:30  wk
    - show maxDuration only if given

    Revision 1.1  2003/02/18 20:13:47  wk
    - project overview

-->
<form name="filterForm" action="{$_SERVER['PHP_SELF']}" method="post">
<input type="hidden" name="filter" value="{$filter}">
<table class="poutline" width="50%">
	<tr>
    	<td class="pButtons" align="left" nowrap>
			<input type="submit" value="Active projects" class="button" id="{$active}" onclick="javascript:document.filterForm['filter'].value='active';" />
        </td>
    	<td class="pButtons" align="left" nowrap>
			<input type="submit" value="Closed projects" class="button" id="{$closed}"  onclick="javascript:document.filterForm['filter'].value='closed';" />
        </td>
    	<td class="pButtons" align="left" nowrap>
			<input type="submit" value="All projects" class="button" id="{$all}"  onclick="javascript:document.filterForm['filter'].value='all';"/>
        </td>       
     </tr>
</table>
</form>

<table class="outline">
    <tr>
        <th nowrap="nowrap">project</th>
        <th nowrap="nowrap">effort</th>
        <th nowrap="nowrap">max. effort</th>
        <th nowrap="nowrap">effort in %</th>
        <th nowrap="nowrap">&nbsp;</th>
    </tr>
    {foreach($times as $aTime)}
        { $class = $aTime['_isProjectAvail']?'':' class="disabled"'}
        <tr>
            <td {$class}>{$aTime['_name']}</td>
            <td {$class} align="right" nowrap="nowrap">
                {$aTime['_durationSum']} h
            </td>
            <td {$class} align="right" nowrap="nowrap">
                {if ($aTime['maxDuration'])}
                    {$aTime['maxDuration']} h
                {else}
                    &nbsp;
            </td>
            <td {$class} align="right" nowrap="nowrap">
                {if ($aTime['maxDuration'])}
                    {$aTime['_percent']}%
                {else}
                    &nbsp;
            </td>
            <td {$class}>
                {if ($aTime['_width'])}
                    <span style="background-color:{$aTime['_color']};">
                        <img src="pixel" width="{$aTime['_width']}" height="10"/>
                    </span>
            </td>
        </tr>
</table>
