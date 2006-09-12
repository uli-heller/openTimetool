<!--
    $Log: footer.tpl,v $
    Revision 1.5  2003/02/13 16:19:33  wk
    - nicer footer

    Revision 1.4  2002/11/12 15:31:10  wk
    - dont show the process-timer

    Revision 1.3  2002/11/11 18:03:51  wk
    - use config->isLiveMode
    - some new config-stuff

    Revision 1.2  2002/08/29 13:31:01  wk
    - benchmarking

    Revision 1.1.1.1  2002/07/22 09:37:37  wk


-->
<!--     
{if( !$config->isLiveMode() )}
<p><center>
<h3>Just the timer output; set comments around this section in footer.tpl to get rid of it ... </h3>
    { $processingTimer->stop()}
    {$processingTimer->getOutput()}
</center>   
-->
<br><br>

</body>
</html>
