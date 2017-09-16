<!--

$Id$

-->

<table>
{foreach($thanks as $aThanks)}
    <tr>
        <td>
            <a href="{$aThanks['projectUrl']}" target="_blank">
                {$aThanks['project']}
            </a>
        </td>

        <td>
            {$aThanks['comment']}
        </td>

        <td>
        {if($aThanks['authorUrl'])}
            <a href="{$aThanks['authorUrl']}" target="_blank">
                {$aThanks['author']?$aThanks['author']:'author(s)'}
            </a>
        {else}
            &nbsp;
        </td>
<!--
        <td>
            <a href="{$aThanks['copyrightUrl']}" alt="">copyright</a>
        </td>
-->
    </tr>
</table>

<br><br>
{foreach($thanks as $aThanks)}
    {if($aThanks['logoUrl'])}
        <img src="{$aThanks['logoUrl']}" alt="" height="50">
