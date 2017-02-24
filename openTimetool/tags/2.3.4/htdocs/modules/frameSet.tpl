<!--
    $Log: frameSet.tpl,v $
    Revision 1.1.1.1  2002/07/22 09:37:37  wk


-->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
    <title>Ignaz</title>

<script type="text/javascript" language="JavaScript">
    function winWidth()
    \{
        if (window.innerWidth) return window.innerWidth;
        else if (document.body && document.body.offsetWidth) return document.body.offsetWidth;
        else return 0;
    \}

    function winHeight()
    \{
        if (window.innerHeight) return window.innerHeight;
        else if (document.body && document.body.offsetHeight) return document.body.offsetHeight;
        else return 0;
    \}
    function winRebuild()
    \{
        if (Weite != winWidth() || Hoehe != winHeight())
        \{
            window.moveTo((screen.availWidth - 780) / 2,(screen.availHeight - 460) / 2);
            window.resizeTo(780,460);
        \}
    \}

    // observe ns
    if (!window.Weite && window.innerWidth)
    \{
        window.onresize = winRebuild;
        Weite = winWidth();
        Hoehe = winHeight();
    \}
</script>

    <!-- frames -->
    <frameset rows="75,*,60" frameborder="no" border="0" framespacing="0" cols="*">

        <frame name="headlineFrame" marginwidth="0" marginheight="0" leftmargin="0" topmargin="0" scrolling="no" framespacing="0" border="0" frameborder="no" frameborder="0" src="{$_REQUEST['frameSrc']['headline']}" id="headlineFrame" scrolling="no" noresize>

        <frameset cols="{$cols},*" frameborder="no" border="0" framespacing="0" rows="*">
            <frame name="contentFrame" marginwidth="0" marginheight="0" leftmargin="0" topmargin="0" framespacing="0" border="0" frameborder="no" frameborder="0" src="{$_REQUEST['frameSrc']['content']}" id="contentFrame" scrolling="auto" noresize>
            <frame name="content1Frame" marginwidth="0" marginheight="0" leftmargin="0" topmargin="0" scrolling="no" framespacing="0" border="0" frameborder="no" frameborder="0" src="{$_REQUEST['frameSrc']['content1']}" id="content1Frame" noresize>
        </frameset>

        <frame name="navigationFrame" marginwidth="0" marginheight="0" leftmargin="0" topmargin="0" scrolling="no" framespacing="0" border="0" frameborder="no" frameborder="0" src="{$_REQUEST['frameSrc']['navigation']}" id="bottomFrame" noresize>
    </frameset>

</head>

<script type="text/javascript" language="JavaScript">
    window.document.title="Ignaz";
</script>

<body>
<script type="text/javascript" language="JavaScript">
    // observe ie
    if (!window.Weite && document.body && document.body.offsetWidth)
    \{
        window.onresize = winRebuild;
        Weite = winWidth();
        Hoehe = winHeight();
    \}
</script>
</body>
</html>