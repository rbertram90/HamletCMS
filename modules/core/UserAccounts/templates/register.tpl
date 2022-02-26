<!DOCTYPE html>
<html>
    <head>
        <title>{$page_title} - powered by HamletCMS</title>
        <link rel="stylesheet" href="/hamlet/css/semantic.css" type="text/css">
        <link rel="stylesheet" href="/hamlet/css/blogs_stylesheet.css" type="text/css">
        <script src="/hamlet/resources/js/jquery-3.3.1.min.js" type="text/javascript"></script>
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
            
            <form method="POST" class="ui form">
                <h2 class="ui header">Create an Account</h2>
                <p>Complete the following fields to get access to HamletCMS. The account is free for life!</p>
                
                <div class="ui error message"></div>
                
                <h2 class="ui header">About You</h2>
                <div class="two fields">
                    <div class="field">
                        <label for="fld_name">First Name</label>
                        <input type="text" name="fld_name" />
                    </div>
                    <div class="field">
                        <label for="fld_surname">Surname</label>
                        <input type="text" name="fld_surname" />
                    </div>
                </div>
                <div class="field">
                    <label for="fld_email">Email</label>
                    <input type="text" name="fld_email" />
                </div>
                <div class="field">
                    <label for="fld_email_2">Re-type Email</label>
                    <input type="text" name="fld_email_2" />
                </div>

                <h2 class="ui header">Your Account</h2>
                <div class="field">
                    <label for="fld_username">Username</label>
                    <input type="text" name="fld_username" />
                </div>
                <div class="field">
                    <label for="fld_password">Password</label>
                    <input type="password" name="fld_password" />            
                </div>
                <div class="field">
                    <label for="fld_password_2">Re-type Password</label>
                    <input type="password" name="fld_password_2" />
                </div>

                <input type="button" name="fld_cancel_registration" value="Cancel" class="ui button right floated" onclick="window.history.back();">
                <input type="submit" name="fld_submit_registration" value="Submit" class="ui button teal right floated">
            </form>
            
            <p>All Fields Required</p>
            
            <script>
            $('.ui.form').form({
                fields: {
                  fld_name     : 'empty',
                  fld_surname  : 'empty',
                  fld_username : 'empty',
                  fld_password : ['minLength[6]', 'empty'],
                  fld_password_2 : 'match[fld_password]',
                  fld_email    : ['email', 'empty'],
                  fld_email_2  :  'match[fld_email]',
                }
            });
            </script>
        </div>
        
    </body>
</html>