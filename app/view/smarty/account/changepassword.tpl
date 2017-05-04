{viewCrumbtrail(array("/account", 'Account'), 'Change Password')}
{viewPageHeader('Change Password', 'lock.png')}

<form action="/account/changepassword/submit" method="POST">
    <label for="fld_password">Current Password (*)</label>
	<input type="password" name="fld_current_password" onkeyup="validate(this,{ldelim}password:true{rdelim})" />			
    
	<label for="fld_new_password">Create New Password (*)</label>
	<input type="password" name="fld_new_password" />
    
	<label for="fld_new_password_2">Re-type New Password (*)</label>
	<input type="password" name="fld_new_password_rpt" />
    
	<div style="text-align:right; width:100%;">
        <input type="submit" name="fld_submit_passwordchange" value="Change Password" class="button_blue" />
    </div>
</form>