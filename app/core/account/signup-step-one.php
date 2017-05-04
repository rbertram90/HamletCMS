<div style="width:60%; margin: 0 auto;">
<form action="/register" method="POST" onsubmit="return checkForm(this);">
	<div class="form-wrapper">
		<h2>About You</h2>
		
		<label for="fld_name">First Name</label>
		<input type="text" name="fld_name" onkeyup="validate(this,{fieldlength:1})" />
		
		<label for="fld_surname">Surname</label>
		<input type="text" name="fld_surname" />
		
		<label for="fld_email">Email</label>
		<input type="text" name="fld_email" onkeyup="validate(this,{email:true})" />
		
		<label for="fld_email_2">Re-type Email</label>
		<input type="text" name="fld_email_2" />
	</div>
	
	<div class="form-wrapper">
		<h2>Account Set-up</h2>
		
		<label for="fld_username">Username</label>
		<input type="text" name="fld_username" />
		
		<label for="fld_password">Password</label>
		<input type="password" name="fld_password" onkeyup="validate(this,{password:true})" />			
		
		<label for="fld_password_2">Re-type Password</label>
		<input type="password" name="fld_password_2" />
	</div>
	
	<div class="form-wrapper" style="text-align:right;">
		<input type="button" name="fld_cancel_registration" value="Cancel" class="button_grey" onclick="redirect('index.php')" />
		<input type="submit" name="fld_submit_registration" value="Submit" class="button_blue" style='font-weight:bold;' />
	</div>
</form>
<p>All Fields Required</p>
</div>