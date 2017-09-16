<!--

$Id$

-->

{if($isAdmin)}

    <a href="{$_SERVER['PHP_SELF']}">TSM &middot; openTimetool System Menu</a>
    <br><br><br>

    {if($mode=='tmpdir' && !$config->demoMode)}
        {if(@$id)}
            <form action="{$_SERVER['PHP_SELF']}" method="post" name="tmpdir">
                <input type="hidden" name="id" value="{$id}">
                <input type="hidden" name="mode" value="{$mode}">
                <input type="submit" value="clear tmp dir" class="button">
            </form>

        {else}
            <b>removed tmp files and dirs:</b><br>
            <pre>{$output}</pre>

    {elseif($mode=='opcache' && $opcache)}
        {if(@$id)}
            <form action="{$_SERVER['PHP_SELF']}" method="post" name="opcache">
                <input type="hidden" name="id" value="{$id}">
                <input type="hidden" name="mode" value="{$mode}">
                <input type="submit" value="clear php opcache" class="button">
            </form>

        {else}
            <b>clear php opcache:</b><br>
            <pre>{$output}</pre>

    {else}
        <ul style="margin:0;padding-left:20px;">
            <li>
            {if(!$config->demoMode)}
                <a href="{$_SERVER['PHP_SELF']}?mode=tmpdir">clear tmp dir</a>
            {else}
                clear tmp dir
            <br><br></li>
            <li>
            {if($opcache)}
                <a href="{$_SERVER['PHP_SELF']}?mode=opcache">clear php opcache</a>
            {else}
                clear php opcache
            <br><br></li>
            <li>
            {if($config->phpInfo)}
                <a href="{$_SERVER['PHP_SELF']}?mode=phpinfo" target="phpinfo">php infos</a>
            {else}
                php infos
            <br><br></li>
        </ul>
