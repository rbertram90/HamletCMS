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
                <img src="/images/logo.png" alt="HamletCMS" />
            </div>
            
            <h1>Welcome</h1>
            {foreach from=$messages item=$message}
                <div class="ui message {$message.type}">{$message.text}</div>
            {/foreach}
            
            <form action="/cms/account/login" method="POST" class="ui form">
                <div class="field">
                    <label for="fld_username">Username</label>
                    <input type="text" name="fld_username" value="" required>
                </div>
                
                <div class="field">
                    <label for="fld_password">Password</label>
                    <input type="password" name="fld_password" required>
                </div>

                {if $registerAllowed}
                    <a href="/cms/account/register">Register new account</a> | 
                {/if}
                <a href="/cms/account/resetpassword">Reset password</a>
                
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