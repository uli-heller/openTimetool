<!--

$Id$

-->

{if($showLogin)}
    <br>
    <!--go back to: {$userAuth->getRequestedUrl()}-->
    <form method="post" action="{$userAuth->getRequestedUrl()}" name="loginForm">
        <table class="outline">
            <thead>
                <tr>
                    <th colspan="2">Login</th>
                </tr>
            </thead>

            <tfoot>
                <tr>
                    <td>&nbsp;</td>
                    <td align="left">
                        <input type="submit" value="log in" class="button">
                        <!-- input type="reset" value="reset" class="button" -->
                    </td>
                </tr>
            </tfoot>

            <tbody>
                <tr>
                    <td>user</td>
                    <td align="left"><input type="text" name="{$userAuth->getInputUsername()}"></td>
                </tr>

                <tr>
                    <td>password</td>
                    <td align="left"><input type="password" name="{$userAuth->getInputPassword()}"></td>
                </tr>
            </tbody>
        </table>
    </form>
    <br>

    <script>
        document.loginForm.{$userAuth->getInputUsername()}.focus();
    </script>
