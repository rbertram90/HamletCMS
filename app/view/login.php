<?php
    $username = "";
    $message= "";
    $continue = true;

    // really quick screen to log in
    if(isset($_POST['fld_username']) && isset($_POST['fld_password']))
    {
        $username = sanitize_string($_POST['fld_username']);
        $password = sanitize_string($_POST['fld_password']);
        
        if(strlen($username) == 0)
        {
            $message = "<div class='ui error message'>Username not entered</div>";
            $continue = false;
        }
        elseif(strlen($password) == 0)
        {
            $message = "<div class='ui error message'>Please complete the password field</div>";
            $continue = false;
        }
        
        if($continue)
        {
            $account = new rbwebdesigns\AccountManager($cms_db);

            if($account->login($username, $password))
            {
                redirect('/');
            }
            else
            {
                $message = "<div class='ui error message'>Login Failed</div>";
            }
        }
    }

    if(isset($_GET['newaccount']))
    {
        if($_GET['newaccount'] == 1)
        {
            $message = "<div class='ui success message'>Account Created</div>";
        }
    }

?><!DOCTYPE html>
<html>
    <head>
        <title>Login</title>
        <link rel="stylesheet" href="/css/semantic.css" type="text/css">
        <link rel="stylesheet" href="/css/blogs_stylesheet.css" type="text/css">
        <script src="/js/semantic.js" type="text/javascript"></script>
    </head>
    <body>
        <style>
            body {
                min-width: inherit;
            }
        </style>
        <div id="loginbox">
            <div id="logoholder">
                <img src="/images/logo.png" alt="Blog CMS" />
            </div>
            
            <h1>Welcome</h1>
            <?=$message;?>
            
            <form action="/" method="POST" class="ui form">
                
                <div class="ui error message"></div>
                
                <div class="field">
                    <label for="fld_username">Username</label>
                    <input type="text" name="fld_username" value="<?=$username?>" />
                </div>
                
                <div class="field">
                    <label for="fld_password">Password</label>
                    <input type="password" name="fld_password" />
                </div>

                <a href="/newuser">Register new account</a>
                
                <button class="ui right floated teal button">Login &nbsp;&#10095;</button>
                <div class="clear"></div>
            </form>
            
            
            <script>
            $('.ui.form').form({
                fields: {
                  fld_username : 'empty',
                  fld_password : 'empty'
                }
            });
            </script>
        </div>
    </body>
</html>