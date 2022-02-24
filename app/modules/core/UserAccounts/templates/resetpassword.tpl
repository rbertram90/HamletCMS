<!DOCTYPE html>
<html>
    <head>
        <title>{$page_title}</title>
        <link rel="stylesheet" href="/hamlet/css/semantic.css" type="text/css">
        <link rel="stylesheet" href="/hamlet/css/blogs_stylesheet.css" type="text/css">
        <script src="/hamlet/js/semantic.js" type="text/javascript"></script>
    </head>
    <body>
        <style>
            body {
                min-width: inherit;
            }
        </style>
        <div id="loginbox">
            <div id="logoholder">
                <img src="/hamlet/images/logo.png" alt="HamletCMS" />
            </div>
            
            <h1>Reset password</h1>
            {foreach from=$messages item=$message}
                <div class="ui message {$message.type}">{$message.text}</div>
            {/foreach}
            
            <form method="POST" class="ui form">
                {if isset($email) && isset($token)}
                    <div class="field">
                        <label for="password">New password</label>
                        <input type="password" name="password" />
                    </div>
                    <div class="field">
                        <label for="password_repeat">Re-type password</label>
                        <input type="password" name="password_repeat" />
                    </div>
                    <input type="hidden" name="email" value="{$email}" />
                    <input type="hidden" name="token" value="{$token}" />
                {else}
                    <div class="field">
                        <label for="email">Email</label>
                        <input type="text" name="email" id="email" value="" required>
                    </div>
                {/if}


                <a href="/cms/account/login">Login</a> | <a href="/cms/account/register">Register new account</a>
                
                <button class="ui right floated teal button">Reset &nbsp;&#10095;</button>
                <div class="clear"></div>
            </form>
        </div>
    </body>
</html>