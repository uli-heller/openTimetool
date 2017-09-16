<!--

$Id$

-->

{%include common/macro/time.mcr%}

<center>
    <form action="{$_SERVER['PHP_SELF']}" method="post">

    {if( $exportIt )}
        {if( isset($_REQUEST['action_print']) )}
            <script>
                window.setTimeout("window.print()", 1000);
            </script>
    {else}
        <table class="outline" width="90%">
            <tr>
                <td>export</td>
                <td>
                    <input type="submit" name="action_print" value="print" class="button">  &nbsp;
                    <input type="submit" name="action_toPDF" value="to PDF" class="button">  &nbsp;
                </td>
            </tr>

            <tr>
                <td>&nbsp;</td>
                <td>
                    <input type="submit" value="update page" class="button"> &nbsp;
                    <input type="submit" name="showAllColumns" value="show all columns" class="button">
                </td>
            </tr>
        </table>

    <br>
    <!-- AK isset instead of sizeof -->
    {if(isset($times))}
        { $durationSum = 0}
        <table class="outline" width="90%">

            {if( !$exportIt )}
                <thead>
                    <tr>
                    {if( $showCols['start'] )}
                        <th>Start</th>
                    {if( $showCols['project'] )}
                        <th>Project</th>
                    {if( $showCols['comment'] )}
                        <th>Comment</th>
                    {if( $showCols['task'] )}
                        <th>Task</th>
                    {if( $showCols['duration'] )}
                        <th>Duration</th>
                    </tr>
                </thead>

            {if( !$exportIt )}
                <tbody>
                    <tr>
                    {if( $showCols['start'] )}
                        <td align="left">
                            <input type="checkbox" name="cols[start]" checked="checked" onchange="this.form.submit()">
                        </td>
                    {if( $showCols['project'] )}
                        <td align="left">
                            <input type="checkbox" name="cols[project]" checked="checked" onchange="this.form.submit()">
                        </td>
                    {if( $showCols['comment'] )}
                        <td align="left">
                            <input type="checkbox" name="cols[comment]" checked="checked" onchange="this.form.submit()">
                        </td>
                    {if( $showCols['task'] )}
                        <td align="left">
                            <input type="checkbox" name="cols[task]" checked="checked" onchange="this.form.submit()">
                        </td>
                    {if( $showCols['duration'] )}
                        <td align="left">
                            <input type="checkbox" name="cols[duration]" checked="checked" onchange="this.form.submit()">
                        </td>
                    </tr>
                </tbody>
        </table>

        <table class="outline" width="90%" cellspacing="0" cellpadding="0">
            {if( !$showCols['project'] )}
                <thead>
                    <tr>
                        <td colspan="{$numCols}">
                            <br>
                            <b>Project: {$projectTreeDyn->getPathAsString($times[0]['projectTree_id'])}</b>
                        </td>
                    </tr>
                </thead>

            <tbody>
                <!-- some intial settings to avoid notices : AK -->
                { $lastUid = -1 }
                { $durationSecSum = 0 }
                { $durationSecDay = 0 }
                { $lastDate = 0 }
                {foreach($times as $aTime)}
                    { $class='' }
                    {if( $aTime['id'] == @$_REQUEST['id'] )}
                        { $class = 'class="backgroundHighlight"' }
                    <tr>
                    {if( $aTime['_user_id'] != $lastUid )}
                        <td valign="top" nowrap="nowrap" style="height:40px;" colspan="{$numCols}">
                            <br>
                            <b>User: {$aTime['_user_name']}&nbsp;{$aTime['_user_surname']}</b>
                        </td>
                    </tr>

                    {if( $aTime['_user_id'] != $lastUid )}
                        <tr>
                        {if( $showCols['start'] )}
                            <th>Start</th>
                        {if( $showCols['project'] )}
                            <th>Project</th>
                        {if( $showCols['comment'] )}
                            <th>Comment</th>
                        {if( $showCols['task'] )}
                            <th>Task</th>
                        {if( $showCols['duration'] )}
                            <th style="text-align:right;" align="right">Duration</th>
                        </tr>

                    <tr>
                    { $curDate = $dateTime->formatDateFull($aTime['timestamp'])}
                    {if( $curDate != $lastDate || $aTime['_user_id'] != $lastUid)}
                        {if( !empty($lastDate))}
                            {if( $showCols['start'] )}
                                <td valign="top" {$class} style="border-top:1px solid #C7C7C7;">
                                    &nbsp;
                                </td>
                            {if( $showCols['project'] )}
                                <td valign="top" {$class} style="border-top:1px solid #C7C7C7;">
                                    &nbsp;
                                </td>
                            {if( $showCols['comment'] )}
                                <td valign="top" {$class} style="border-top:1px solid #C7C7C7;">
                                    &nbsp;
                                </td>
                            {if( $showCols['duration'] )}
                                <td style="border-top:1px solid #C7C7C7;text-align:left;height:30px;vertical-align:top;" align="left">
                                    Sum
                                </td>
                                <td style="border-top:1px solid #C7C7C7;text-align:right;vertical-align:top;" align="right">
                                    {$time->_calcDuration(@$durationSecDay)}&nbsp;h 
                                    { @$durationSecDay = 0; }
                                </td>
                            </tr>

                            <tr>
                        {else}
                            <tr>
                                <td colspan="{$numCols}" style="text-align:right;height:10px;vertical-align:top;">
                                </td>
                            </tr>

                        <td valign="top" colspan="{$numCols}" style="border:1px solid #C7C7C7;border-bottom:0;background-color:#eee;">
                            <b>{$curDate}</b>
                        </td>
                        </tr>

                        <tr>

                    { $lastDate = $curDate}
                    { $lastUid=$aTime['_user_id']}  <!-- to check this in the date part too -->

                    {if( $showCols['start'] )}
                        {if( $lastCol=='start' )}
                            { $borderr='border-right:1px solid #C7C7C7;' }
                        {else}
                            { $borderr='' }
                    {if( $showCols['start'] )}
                        <td valign="top" {$class} style="{$borderr}border-left:1px solid #C7C7C7;border-bottom:1px solid #eee;">
                            {echo date('H:i',$aTime['timestamp'])}
                        </td>
                    {if( $showCols['project'] )}
                        {if( $lastCol=='project' )}
                            { $borderr='border-right:1px solid #C7C7C7;' }
                        {else}
                            { $borderr='' }
                    {if( $showCols['project'] )}
                        <td valign="top" {$class} style="{$borderr}border-bottom:1px solid #eee;">
                            {if($aTime['_task_needsProject'])}
                                {$projectTreeDyn->getPathAsString($aTime['projectTree_id'])}
                            &nbsp;
                        </td>
                    {if( $showCols['comment'] )}
                        {if( $lastCol=='comment' )}
                            { $borderr='border-right:1px solid #C7C7C7;' }
                        {else}
                            { $borderr='' }
                    {if( $showCols['comment'] )}
                        <td valign="top" {$class}  style="{$borderr}border-bottom:1px solid #eee;">
                            {echo nl2br($aTime['comment'])}&nbsp;
                        </td>
                    {if( $showCols['task'] )}
                        {if( $lastCol=='task' )}
                            { $borderr='border-right:1px solid #C7C7C7;' }
                        {else}
                            { $borderr='' }
                    {if( $showCols['task'] )}
                        <td valign="top" nowrap="nowrap" {$class}  style="{$borderr}border-bottom:1px solid #eee;">
                            {$aTime['_task_name']}
                        </td>

                    {if(isset($aTime['duration']))}
                        { $durationSecSum+=$aTime['durationSec'];
                          $durationSecDay+=$aTime['durationSec']}
                    {if( $showCols['duration'] )}
                        <td valign="top" nowrap="nowrap" align="right" {$class} style="border-right:1px solid #C7C7C7;border-bottom:1px solid #eee;">&nbsp;
                            {if(isset($aTime['duration']))}
                                {$aTime['duration']}&nbsp;h
                        </td>
                    </tr>

                <tr>
                {if( $showCols['start'] )}
                    <td valign="top" {$class} style="border-top:1px solid #C7C7C7;">
                        &nbsp;
                    </td>
                {if( $showCols['project'] )}
                    <td valign="top" {$class} style="border-top:1px solid #C7C7C7;">
                        &nbsp;
                    </td>
                {if( $showCols['comment'] )}
                    <td valign="top" {$class}  style="border-top: 1px solid #C7C7C7;">
                        &nbsp;
                    </td>
                {if( $showCols['task'] )}
                    <td style="border-top:1px solid #C7C7C7;text-align:left;height:30px;vertical-align:top;" align="left">
                    {if( $showCols['duration'] )}
                        Sum
                    </td>  
                {if( $showCols['duration'] )}
                    <td style="border-top:1px solid #C7C7C7;text-align:right;vertical-align:top;" align="right">
                        {$time->_calcDuration(@$durationSecDay)}&nbsp;h 
                    </td>
                </tr>
            </tbody>

            <tfoot>
                <tr>
                {if( $showCols['duration'] )}
                    <td colspan="{$numCols-1}" align="right" valign="top">
                        <b>Sum</b>
                    </td>
                {if( $showCols['duration'] )}
                    <td align="right">
                        <b>
                        {$time->_calcDuration($durationSecSum)}&nbsp;h<br>
                        {$time->_calcDuration($durationSecSum,'decimal')}&nbsp;h<br>
                        {$time->_calcDuration($durationSecSum,'days')}&nbsp;d
                        </b>
                    </td>
                </tr>
            </tfoot>
        </table>
    </form>
</center>
