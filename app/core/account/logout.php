<?php
/***********************************************************************************
    logout.php
    @description log out of rbwebdesigns
	@author R Bertram
	@date 30 DEC 2012

************************************************************************************/

	// Resume the session
	session_start();

	// Kill the session completely
	session_destroy();
	
	// Navigate to homepage
	header("location: index.php");
?>