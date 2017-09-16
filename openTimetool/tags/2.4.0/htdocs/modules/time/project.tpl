<!--

$Id$

-->

<form name="filterForm" action="{$_SERVER['PHP_SELF']}" method="post">
    <input type="hidden" name="filter" value="{$filter}">
    <table class="poutline" width="50%">
        <tr>
            <td class="pButtons" align="left" nowrap="nowrap">
                    <input type="submit" value="Active projects" class="button" id="{$active}" onclick="javascript:document.filterForm['filter'].value='active';">
            </td>
            <td class="pButtons" align="left" nowrap="nowrap">
                    <input type="submit" value="Closed projects" class="button" id="{$closed}" onclick="javascript:document.filterForm['filter'].value='closed';">
            </td>
            <td class="pButtons" align="left" nowrap="nowrap" width="100%">
                    <input type="submit" value="All projects" class="button" id="{$all}" onclick="javascript:document.filterForm['filter'].value='all';">
            </td>       
        </tr>
    </table>
</form>

<table class="outline">
    <thead>
        <tr>
            <th nowrap="nowrap">project</th>
            <th nowrap="nowrap">effort</th>
            <th nowrap="nowrap">max. effort</th>
            <th nowrap="nowrap">effort in %</th>
            <th nowrap="nowrap">&nbsp;</th>
        </tr>
    </thead>

    <tbody>
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
                    <img src="pixel.gif" width="{$aTime['_width']}" height="10" alt="">
                </span>
            </td>
        </tr>
    </tbody>
</table>
