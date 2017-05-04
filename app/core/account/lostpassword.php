<div style="margin:0 auto; width:960px;">

<?php
    // The security question has been answered
    if(isset($_POST['fld_security_a'])):
    
        // Sanitize the e-mail
        $email = safeEmail($_POST['hfld_email']);   
        
        // Check the answer against the email given in step 1
        $a = strtolower(safeString($_POST['fld_security_a']));
        
        // Get the stored answer
        $user = $this->mdlUsers->getUserByEmail($email);
        
        if($a == strtolower($user['security_a'])):
            // success! show the new password
            // This would normally be emailed to the user!
            
            $newpassword = $this->mdlUsers->resetPassword($user['id']);
?>

            <div class="form-wrapper-fixed">
                <h2>Password Changed!</h2>
                <p>Your new password is</p>
                <p style="text-align:center; font-weight:bold;"><?=$newpassword?></p>
                <p>You can now return to the homepage and login, it is suggested that you change your password to something you will remember once you login!</p>
                <a href='<?=CLIENT_ROOT?>/' class="button_blue">Home Page</a>
            </div>

<?php else: ?>

    <div class="form-wrapper-fixed">
        <h2>Recovery Failed</h2>
        <p class="info">Security Answer Incorrect</p>
        <div class="push-right">
            <button onclick='window.location="<?=CLIENT_ROOT?>/"' class="button_blue">Return Home</button>
            <button onclick='window.location="<?=CLIENT_ROOT?>/account/resetpassword"' class="button_orange">Try Again</button>
        </div>
    </div>

<?php
        endif;
        
    elseif(isset($_POST['fld_email'])):
    
        // Sanitize the e-mail
        $email = safeEmail($_POST['fld_email']);
        
        // Get security question
        $user = $this->mdlUsers->getUserByEmail($email);

        if($user != null):
?>
    <div class="form-wrapper-fixed">
    <h2>Password Recovery - Security Question</h2>

    <p><?=$user['security_q']?></p>
    
    <form action="<?=CLIENT_ROOT?>/account/resetpassword" method="POST">
        <input type="text" name="fld_security_a" />
        <input type="hidden" name="hfld_email" value="<?=$email;?>" />
        <div class="push-right">
            <input type="submit" value="Next Step" name="submit_answer" class="button_blue" />
        </div>
    </form>
    </div>
    
<?php   else: ?>

    <div class="form-wrapper-fixed">
        <h2>Recovery Failed :(</h2>
        <p class="info">E-mail address not found!</p>
        <div class="push-right">
            <button onclick='window.location="<?=CLIENT_ROOT?>/"' class="button_blue">Return Home</button>
            <button onclick='window.location="<?=CLIENT_ROOT?>/account/resetpassword"' class="button_orange">Try Again</button>
        </div>
    </div>
    
<?php   endif;
    else:
?>
    <div class="form-wrapper-fixed">
    <h2>Password Recovery</h2>
    <p>Enter your e-mail address for you account</p>
    
    <form action="<?=CLIENT_ROOT?>/account/resetpassword" method="POST">
        <input type="text" name="fld_email" />
        <div class="push-right">
            <input type="submit" value="Next Step" name="stage2" class="button_blue" />
        </div>
    </form>
    </div>

<?php endif; ?>

</div>