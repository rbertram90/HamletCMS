<?php
/***********************************************************************************
    login.php
    @description log in to rbwebdesigns
	@author R Bertram
	@date 30 DEC 2012

************************************************************************************/

	// Include the main setup file
	require_once 'setup.inc.php';

// Main Login Process
function login($strUsername, $strPassword, $con) {
	
	// Inputs
	$username = safeString($username);
	$password = md5(safeString($password));
	
    try {
        // Find the user id from username and password provided
        $query_user = $con->prepare('SELECT id,admin FROM '.TBL_USERS.' WHERE username= :usnm AND password= :pswd');
        
        $query_user->execute(array(
            'usnm' => $username,
            'pswd' => $password
        ));
    }
    catch(PDOException $e) {
        echo showError('PDO ERROR: '.$e->getMessage());
    }
    
	if(!$query_user) {
		// failed query
		die(showError("Error with MySQL Query: login"));
	}
	else {
		if($query_user->rowCount() == 1) {
		
			// Found 1 match =)
			$fetch_user = $query_user->fetch();
			
			// Set the user that is logged in
			$_SESSION['userid'] = $fetch_user[0];
			$_SESSION['admin'] = $fetch_user[1];
			
			// Return Success
			return true;
		}
		else {
			// No user found with username and password
			return false;
		}
	}
}
	
	// Call login	
	if(!login($_POST['fld_username'], $_POST['fld_password'], $dbc)) {
		die('Login Failed');
	}

	// Where are we logging in from?
	$referrer = strtolower($_SERVER['HTTP_REFERER']);
?>    
	<link rel="stylesheet" type="text/css" href="css/main.css"/>
	<div class="outstanding_white">
	<h1>Login Successful!</h1>
<?php
	if(strpos($referrer,"sample3") !== false) {
		$linkpath = CLIENT_ROOT."/sample3/dashboard.php";
		$linkimage = CLIENT_ROOT."/sample3/images/preview.png";
		$linktext = "Sample3";
	} elseif(strpos($referrer,"digipix") !== false) {
		$linkpath = CLIENT_ROOT."/digipix/dashboard.php";
		$linkimage = CLIENT_ROOT."/digipix/images/logo.png";
		$linktext = "DigiPix";
	} else {
		$linkpath = CLIENT_ROOT."/index.php";
		$linkimage = CLIENT_ROOT."/images/rbwebdesignslogo.jpg";
		$linktext = "RBwebdesigns";
	}
	
	echo "<img src='$linkimage' alt='' width='50%' />";
	echo "<a href='$linkpath' class='button_continue'>Continue to $linktext &gt;&gt;&gt;</a>";
?>
    </div>