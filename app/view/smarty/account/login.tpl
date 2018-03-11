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
            
            <h1>Welcome</h1>
            {foreach from=$messages item=$message}
                <p class="{$message.type}">{$message.text}</p>
            {/foreach}
            
            <form action="/account/login" method="POST" class="ui form">
                
                <div class="ui error message"></div>
                
                <div class="field">
                    <label for="fld_username">Username</label>
                    <input type="text" name="fld_username" value="" required>
                </div>
                
                <div class="field">
                    <label for="fld_password">Password</label>
                    <input type="password" name="fld_password" required>
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