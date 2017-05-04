<?php
/*
 * login_form.php
 * Description: front end code for the login form to log into sample3
 * Uses AJAX to validate and submit the request
 */
?>
<form action="<?=CLIENT_ROOT?>/account/login" method="POST">
    <label for="fld_username">Username</label>
    <input type="text" name="fld_username" />
    <label for="fld_password">Password</label>
    <input type="password" name="fld_password" />
    <div class="push-right">
        <a href="<?=CLIENT_ROOT?>/register">Create Account</a> - <a href="<?=CLIENT_ROOT?>/account/resetpassword" style="margin-right:10px;">Forgotten password?</a><input type="submit" value="Login" name="fld_submitlogin" class="button_blue" />
    </div>
</form>