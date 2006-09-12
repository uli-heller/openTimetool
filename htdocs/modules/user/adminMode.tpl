<!--
    $Log: adminMode.tpl,v $
    Revision 1.2  2002/11/30 18:38:53  wk
    - remove unnecessary text

    Revision 1.1  2002/11/13 19:02:08  wk
    - show when switching to admin mode

-->     
    
<font class="success">
    {if($isAdmin)}
        Admin mode - ON
        <br><br>
        You have switched to admin mode!
    {else}
        Admin mode - OFF
        <br><br>
        Admin mode has been turned off, now you can work again as if you were a standard user!
</font>

