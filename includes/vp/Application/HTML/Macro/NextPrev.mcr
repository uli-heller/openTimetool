<!--

$Id$

-->

<!--
    @deprectated    use NextPrev_Buttons(&$page) instead!!!
-->
{%macro nextPrevButtons(&$page)%}
    {%NextPrev_Buttons($page)%}

<!--
    this macro prints the next previous logic on the page
    it creates a table therefore in which it puts the links

    @param  object  this is an instance of the vp_Application_HTML_NextPrev-class
-->
{%macro NextPrev_Buttons(&$page)%}
    {global $session}   <!-- FIXXME to be changed -->
    <table>
        <tr>
            <td nowrap="nowrap" align="left">
                {if(isset($page->showPrev))}
                    <a href="{$page->beginLink}">&lt;&lt;</a> &nbsp;
                    <a href="{$page->prevLink}">&lt;</a>
                {else}
                    <!-- to get about the same space as when there are the buttons shown -->
                    &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
            </td>
            <td align="center" nowrap="nowrap">
                {if($page->count/$session->temp->nextPrev->listCount>1)}
                    <!-- Gehe zu Seite -->
                    <!-- are there more then 10 pages with results to show? -->
                    {if($page->count/$session->temp->nextPrev->listCount > 10)}
                        <!-- then handle it in a special way, so we dont flood the page with links
                            to the next pages -->

                        <!-- if there are more then 10 pages until the end -->
                        { $_startLinks=($page->count-$page->listStart)/$session->temp->nextPrev->listCount}
                        {if( $_startLinks > 10 )}
                            { $_startLinks=10}

                        { $_offset = $page->listStart/$session->temp->nextPrev->listCount-4}
                        {if($page->listStart/$session->temp->nextPrev->listCount < 5)}
                            { $_offset = 0}
                        {if($page->listStart/$session->temp->nextPrev->listCount > $page->count/$session->temp->nextPrev->listCount-4)}
                            { $_offset = $page->count/$session->temp->nextPrev->listCount-9}

                        <!-- show always 10 links, and the current one should be more or less in the middle :-) -->
                        {%repeat 10 times $_counter%}
                            <!-- show links to the next 10 pages -->
                            { $_tmp = floor($_counter + $_offset)}
                            {if($_tmp*$session->temp->nextPrev->listCount == $page->listStart)}
                                -{$_tmp+1}-
                            {else}
                                <a href="{$page->urlPrefix}setListStart={$_tmp*$session->temp->nextPrev->listCount}">-{$_tmp+1}-</a>&nbsp;
                    {else}
                        <!-- less then 10 pages of results -->
                        {%repeat $page->count/$session->temp->nextPrev->listCount times $_counter%}
                            {if($_counter*$session->temp->nextPrev->listCount == $page->listStart)}
                                -{$_counter+1}-
                            {else}
                                <a href="{$page->urlPrefix}setListStart={$_counter*$session->temp->nextPrev->listCount}">-{$_counter+1}-</a>&nbsp;
            </td>
            <td nowrap="nowrap" align="right">
                {if(isset($page->showNext))}
                    <a href="{$page->nextLink}">&gt;</a>
                    &nbsp; <a href="{$page->endLink}">&gt;&gt;</a>
                {else}
                    <!-- to get about the same space as when there are the buttons shown -->
                    &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
            </td>
        </tr>
        <tr>
            <td colspan="3" align="center">
                {$page->getText(0)}
                <select name="setListCount" onchange="this.form.submit()" style="text-align:right;">
                    {foreach($page->_availCounts as $_aCount)}
                        <option value="{$_aCount}"
                            {if( $session->temp->nextPrev->listCount == $_aCount )}
                                selected="selected"
                        >{$_aCount}&nbsp;</option>
                </select>{$page->getText(1)}
            </td>
        </tr>
    </table>
