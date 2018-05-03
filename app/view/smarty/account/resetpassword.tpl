<!DOCTYPE html>
<html>
    <head>
        <title>{$page_title}</title>
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
            
            <h1>Reset password</h1>
            {foreach from=$messages item=$message}
                <div class="ui message {$message.type}">{$message.text}</div>
            {/foreach}
            
            <form method="POST" class="ui form">
                <div class="field">
                    <label for="fld_username">Username</label>
                    <input type="text" name="fld_username" id="fld_username" value="" required>
                </div>
                
                <div class="field">
                    <label for="fld_email">Email</label>
                    <input type="text" name="fld_email" id="fld_email" value="" required>
                </div>

                <div class="field">
                    <label for="fld_firstname">First name</label>
                    <input type="text" name="fld_firstname" id="fld_firstname" value="" required>
                </div>

                <div class="field">
                    <label for="fld_surname">Surname</label>
                    <input type="text" name="fld_surname" id="fld_surname" value="" required>
                </div>

                <div class="field">
                    <label for="fld_password">New Password</label>
                    <input type="password" name="fld_password" id="fld_password" value="" required>
                </div>

                <div class="field">
                    <label for="fld_password_rpt">Retype New Password</label>
                    <input type="password" name="fld_password_rpt" id="fld_password_rpt" value="" required>
                </div>

                <a href="/cms/account/login">Login</a> | <a href="/cms/account/register">Register new account</a>
                
                <button class="ui right floated teal button">Reset &nbsp;&#10095;</button>
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