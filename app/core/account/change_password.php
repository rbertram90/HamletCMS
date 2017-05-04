<?php
/******************************************************************************
    change_avatar.php
    @description front-end form to change the users password.
    @author R Bertram
    @date 12 JAN 2013
******************************************************************************/
?>

<form action="<?php echo ROOT;?>index.php?p=account" method="POST">
    <label for="fld_password">Current Password (*)</label>
	<input type="password" name="fld_current_password" onkeyup="validate(this,{password:true})" />			
    
	<label for="fld_new_password">Create New Password (*)</label>
	<input type="password" name="fld_new_password" />
    
	<label for="fld_new_password_2">Re-type New Password (*)</label>
	<input type="password" name="fld_new_password_rpt" />
    
	<div style="text-align:right; width:100%;">
        <input type="submit" name="fld_submit_passwordchange" value="Change Password" class="button_blue" />
    </div>
</form>