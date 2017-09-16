<!--

$Id$

-->

{%include common/macro/time.mcr%}

<form action="{$_SERVER['PHP_SELF']}" method="post">
    {if( $_REQUEST['action_print'] )}
        <script>
            window.setTimeout("window.print()",1000);
        </script>
    {else}
        <input type="submit" value="preview"> &nbsp;
        <input type="submit" name="action_print" value="print">  &nbsp;
    <br><br>

    {if(sizeof($times))}
        { $durationSum = 0}
        <table class="outline">
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

            <tbody>
                {if( !$_REQUEST['action_print'] )}
                    <tr>
                        {if( $showCols['start'] )}
                            <td align="center">
                                <input type="checkbox" name="cols[start]" checked="checked">
                            </td>
                        {if( $showCols['project'] )}
                            <td align="center">
                                <input type="checkbox" name="cols[project]" checked="checked">
                            </td>
                        {if( $showCols['comment'] )}
                            <td align="center">
                                <input type="checkbox" name="cols[comment]" checked="checked">
                            </td>
                        {if( $showCols['task'] )}
                            <td align="center">
                                <input type="checkbox" name="cols[task]" checked="checked">
                            </td>
                        {if( $showCols['duration'] )}
                            <td align="center">
                                <input type="checkbox" name="cols[duration]" checked="checked">
                            </td>
                    </tr>

                {if( !$showCols['project'] )}
                    <tr>
                        <td colspan="{$numCols}">
                            <br>
                            <u>Project: {$projectTree->getPathAsString($times[0]['projectTree_id'])}</u>
                        </td>
                    </tr>

                {foreach($times as $aTime)}
                    { $class='' }
                    {if( $aTime['id'] == $_REQUEST['id'] )}
                        { $class = 'class="backgroundHighlight"' }
                    <tr>
                        {if( $aTime['_user_id'] != $lastUid )}
                                <td valign="top" nowrap="nowrap" colspan="{$numCols}">
                                    <br>
                                    <b>User: {$aTime['_user_name']} {$aTime['_user_surname']}</b>
                                </td>
                            </tr>
                            <tr>

                        { $curDate = date('l, d.m.Y',$aTime['timestamp'])}
                        {if( $curDate != $lastDate || $aTime['_user_id'] != $lastUid)}
                            <td valign="top" colspan="{$numCols}">
                                <b>{$curDate}</b>
                            </td>
                            </tr>
                            <tr>
                        { $lastDate = $curDate}
                        { $lastUid=$aTime['_user_id']}  <!-- to check this in the date part too -->

                        {if( $showCols['start'] )}
                            <td valign="top" {$class}>
                                {echo date('H:i',$aTime['timestamp'])}
                            </td>
                        {if( $showCols['project'] )}
                            <td valign="top" {$class}>
                                {if($aTime['_task_needsProject'])}
                                    {$projectTree->getPathAsString($aTime['projectTree_id'])}
                            </td>
                        {if( $showCols['comment'] )}
                            <td valign="top" {$class}>
                                {echo nl2br($aTime['comment'])}
                            </td>
                        {if( $showCols['task'] )}
                            <td valign="top" nowrap="nowrap" {$class}>
                                {$aTime['_task_name']}
                            </td>

                        {if($aTime['duration'])}
                            { $durationSecSum+=$aTime['durationSec']}
                        {if( $showCols['duration'] )}
                            <td valign="top" nowrap="nowrap" align="center" {$class}>
                                {if($aTime['duration'])}
                                    {$aTime['duration']} h
                            </td>
                    </tr>
                <tr>
                    <td colspan="{$numCols}"><br></td>
                </tr>
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="{$numCols-1}" align="right" valign="top">
                        <b>Sum</b>
                    </td>
                    <td align="center">
                        <b>
                        {$time->_calcDuration($durationSecSum)} h<br>
                        {$time->_calcDuration($durationSecSum,'decimal')} h<br>
                        {$time->_calcDuration($durationSecSum,'days')} d
                        </b>
                    </td>
                </tr>
            </tfoot>
        </table>
</form>
