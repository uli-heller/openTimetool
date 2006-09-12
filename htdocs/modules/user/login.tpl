<!--
    $Log: login.tpl,v $
    Revision 1.12  2003/01/30 16:13:29  wk
    - fix showLogin

    Revision 1.11  2003/01/29 16:06:32  wk
    - show login only when accoun tis active

    Revision 1.10  2002/12/09 13:50:25  wk
    - added some required classes and button

    Revision 1.9  2002/12/09 12:21:41  wk
    - added class=button for IE

    Revision 1.8  2002/11/30 18:39:00  wk
    - properly design

    Revision 1.7  2002/11/28 10:30:38  wk
    - remove warning which we use internally

    Revision 1.6  2002/11/11 18:01:40  wk
    - use config->isLiveMode

    Revision 1.5  2002/10/22 14:42:33  wk
    - changed $auth to $userAuth

    Revision 1.4  2002/09/13 08:59:26  wk
    - focus login field

    Revision 1.3  2002/08/21 20:23:02  wk
    - show internal warning

    Revision 1.2  2002/08/20 09:03:29  wk
    - added a hint

    Revision 1.1.1.1  2002/07/22 09:37:37  wk


//-->
       
{if( $showLogin )}
    <!--go back to: {$userAuth->getRequestedUrl()}-->
    <form method="POST" action="{$userAuth->getRequestedUrl()}" name="loginForm">
        <table class="outline">
            <tr>
                <th colspan="2">Login</th>
            </tr>

            <tr>
                <td>user</td>
                <td align="left"><input type="text" name="{$userAuth->getInputUsername()}"></td>
            </tr>

            <tr>
                <td>password</td>
                <td align="left"><input type="password" name="{$userAuth->getInputPassword()}"></td>
            </tr>

            <tr>
                <td>&nbsp;</td>
                <td align="left">
                    <input type="Submit" value="log in" class="button">
                    <!--
                    <input type="reset" value="reset" class="button">
                    -->
                </td>
            </tr>
        </table>
    </form>                                                            

    <script type="text/javascript" language="JavaScript">
        document.loginForm.{$userAuth->getInputUsername()}.focus();
    </script>
