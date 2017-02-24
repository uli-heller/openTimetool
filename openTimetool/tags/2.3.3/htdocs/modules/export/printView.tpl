<!--

	$Id

	*** switch to SVN ****
    $Log: printView.tpl,v $
    Revision 1.7  2003/03/04 19:13:51  wk
    - use treeDyn

    Revision 1.6  2003/02/14 15:40:48  wk
    - add csv export

    Revision 1.5  2002/12/09 12:20:24  wk
    - added class=button for IE

    Revision 1.4  2002/11/30 18:37:30  wk
    - fix order of times
    - some formatting

    Revision 1.3  2002/11/30 13:03:18  wk
    - use I18N

    Revision 1.2  2002/11/12 15:30:49  wk
    - dont export to html

    Revision 1.1  2002/11/12 13:10:24  wk
    - initial commit

-->

{%include common/macro/time.mcr%}

<center>
    <form action="{$_SERVER['PHP_SELF']}" method="post">

    {if( $exportIt )}
        {if( isset($_REQUEST['action_print']) )}
            <script type="text/javascript" language="JavaScript">
                window.setTimeout("window.print()",1000);
            </script>
    {else}
        <table class="outline" width="90%">
            <tr>
                <td>export</td>
                <td>
                    <input type="Submit" name="action_print" value="print" class="button">  &nbsp;
                    <input type="Submit" name="action_toPDF" value="to PDF" class="button">  &nbsp;
                </td>
            </tr>
            
            <tr>
                <td>&nbsp;</td>
                <td>
                    <input type="Submit" value="update page" class="button"> &nbsp;
                    <input type="Submit" name="showAllColumns" value="show all columns" class="button">
                </td>
            </tr>
        </table>

    <br>
	<!-- AK isset instead of sizeof -->
    {if(isset($times))}
        { $durationSum = 0}
        <table class="outline" width="90%">
        
            {if( !$exportIt )}
                <tr>
                    {if( $showCols['start'] )}
                        <th>Start</th>
                    {if( $showCols['project'] )}
                        <th>Project</th>
                    {if( $showCols['comment'] )}
                        <th>Comment</th>
                    {if( $showCols['task'] )}
                        <th>Task</th>
                    {if( isset($showCols['duration']) )}
                        <th>Duration</th>
                </tr>

            {if( !$exportIt )}
                <tr>
                    {if( $showCols['start'] )}
                        <td align="left">
                            <input type="checkbox" name="cols[start]" checked onChange="this.form.submit()">
                        </td>
                    {if( $showCols['project'] )}
                        <td align="left">
                            <input type="checkbox" name="cols[project]" checked onChange="this.form.submit()">
                        </td>
                    {if( $showCols['comment'] )}
                        <td align="left">
                            <input type="checkbox" name="cols[comment]" checked onChange="this.form.submit()">
                        </td>
                    {if( $showCols['task'] )}
                        <td align="left">
                            <input type="checkbox" name="cols[task]" checked onChange="this.form.submit()">
                        </td>
                    {if( isset($showCols['duration']) )}
                        <td align="left">
                            <input type="checkbox" name="cols[duration]" checked onChange="this.form.submit()">
                        </td>
                </tr>
        </table>
        
        <table class="outline" width="90%" cellspacing="0" cellpadding="0">
            {if( !$showCols['project'] )}
                <tr>
                    <td colspan="{$numCols}">
                        <br>
                        <b>Project: {$projectTreeDyn->getPathAsString($times[0]['projectTree_id'])}</b>
                    </td>
                </tr>


            		
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
                            <td valign="top" nowrap style="height: 40px;" colspan="{$numCols}">
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
                        {if( isset($showCols['duration']) )}
                            <th style="text-align:right;" align="right">Duration</th>
                        </tr>
 
                       
                <tr>

                    { $curDate = $dateTime->formatDateFull($aTime['timestamp'])}
                    {if( $curDate != $lastDate || $aTime['_user_id'] != $lastUid)}
                        {if( !empty($lastDate))}
                            {if( $showCols['start'] )}
                                <td valign="top" {$class} style="border-top: 1px solid #C7C7C7;">
                                &nbsp;
                                </td>
                            {if( $showCols['project'] )}
                                <td valign="top" {$class} style="border-top: 1px solid #C7C7C7;">
                                &nbsp;
                                </td>
                            {if( $showCols['comment'] )}
                                <td valign="top" {$class}  style="border-top: 1px solid #C7C7C7;">
                                 &nbsp;
                                </td>                        
                            <td  style="border-top: 1px solid #C7C7C7; text-align: left; height: 30px; vertical-align: top;" align="left">
                        	   Sum
                            </td>
                            
                            <td style="border-top: 1px solid #C7C7C7; text-align: right; vertical-align: top;" align="right">
                            	{$time->_calcDuration(@$durationSecDay)}&nbsp;h 
                            	{ @$durationSecDay = 0; }
                           </td>
                           </tr>
                       
                       	   <tr>
                       	{else}
                           <tr>
                           <td colspan="{$numCols}" style="text-align: right; height: 10px; vertical-align: top;">
                           </td>
                           </tr>                       	
                                
                       	   
                        <td valign="top" colspan="{$numCols}" style="border: 1px solid #C7C7C7; border-bottom: 0; background-color: #eeeeee;">
                            <b>{$curDate}</b>
                        </td>
                        </tr>
                        
                        <tr>
                        
                    { $lastDate = $curDate}
                    { $lastUid=$aTime['_user_id']}  <!-- to check this in the date part too -->

                    {if( $showCols['start'] )}
                        <td valign="top" {$class} style="border-left: 1px solid #C7C7C7;border-bottom: 1px solid #EEEEEE;">
                            {echo date('H:i',$aTime['timestamp'])}
                        </td>
                    {if( $showCols['project'] )}
                        <td valign="top" {$class} style="border-bottom: 1px solid #EEEEEE;">
                            {if($aTime['_task_needsProject'])}
                                {$projectTreeDyn->getPathAsString($aTime['projectTree_id'])}
                            &nbsp;
                        </td>
                    {if( $showCols['comment'] )}
                        <td valign="top" {$class}  style="border-bottom: 1px solid #EEEEEE;">
                            {echo nl2br($aTime['comment'])}&nbsp;
                        </td>
                    {if( $showCols['task'] )}
                        <td valign="top" nowrap {$class}  style="border-bottom: 1px solid #EEEEEE;">
                            {$aTime['_task_name']}
                        </td>

                    {if(isset($aTime['duration']))}
                        { $durationSecSum+=$aTime['durationSec'];
                          $durationSecDay+=$aTime['durationSec']}
                    {if( isset($showCols['duration']) )}
                        <td valign="top" nowrap align="right" {$class} style="border-right: 1px solid #C7C7C7;border-bottom: 1px solid #EEEEEE;">&nbsp;
                            {if(isset($aTime['duration']))}
                                {$aTime['duration']}&nbsp;h
                        </td>
                </tr>
                
           
           <tr> 
                  {if( $showCols['start'] )}
                        <td valign="top" {$class} style="border-top: 1px solid #C7C7C7;">
                            &nbsp;
                        </td>
                  {if( $showCols['project'] )}
                        <td valign="top" {$class} style="border-top: 1px solid #C7C7C7;">
                            &nbsp;
                        </td>
                  {if( $showCols['comment'] )}
                        <td valign="top" {$class}  style="border-top: 1px solid #C7C7C7;">
                            &nbsp;
                        </td>
                  <td style="border-top: 1px solid #C7C7C7; text-align: left; height: 30px; vertical-align: top;" align="left">
                  	   Sum
                  </td>                
                  <td style="border-top: 1px solid #C7C7C7; text-align: right; vertical-align: top;" align="right">
                       	{$time->_calcDuration(@$durationSecDay)}&nbsp;h 
                   </td>
            </tr>
                   
            <tr>
                <td colspan="{$numCols-1}" align="right" valign="top">
                    <b>Sum</b>
                </td>
                <td align="right">
                    <b>
                    {$time->_calcDuration($durationSecSum)}&nbsp;h<br>
                    {$time->_calcDuration($durationSecSum,'decimal')}&nbsp;h<br>
                    {$time->_calcDuration($durationSecSum,'days')}&nbsp;d
                    </b>
                </td>
            </tr>
        </table>
    </form>
</center>
