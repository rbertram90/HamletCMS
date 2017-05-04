<?php
/************************************************************************************
    ctrl-register.php
	Backend code for registering a new user
	@author R Bertram
	@date 26 DEC 2012

************************************************************************************/
	
	// Validate Input from Form
	$name = safeString($_POST['fld_name']);
	$surname = safeString($_POST['fld_surname']);
	$emailaddr = safeEmail($_POST['fld_email']);
	$username = safeString($_POST['fld_username']);
	$password = safeString($_POST['fld_password']);
	$md5_password = md5($password);
	$signupdate = date("Y-m-d");
	
	// Check that the username is unique
	if(count((array) $gClsUsers->getUserByUsername($username)) > 0) {
		// Found username
		die(showError("Username already taken"));
	}
	
	// Query Database
	$createuser = mysql_query("INSERT INTO ".TBL_USERS." (name,surname,email,username,password,signup_date) VALUES ('$name','$surname','$emailaddr','$username','$md5_password',$signupdate)") or die(showSQLError());
	
	if(!$createuser) {
		// Query failed
		die(showError("Unable to amend database: query createuser"));
	}
	else {
		// Auto login to system!
		if( !login($username,$password) ) {
		
			// Shouldn't occur in this case as just added to the system
			echo showError("No Match found for username and password");
		}
	}
?>