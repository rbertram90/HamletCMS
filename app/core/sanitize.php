<?php
// DEPRECATED FUNCTIONS - here because they've been used EVERYWHERE!
// Sanitize a string
function safeString($str) {
	return filter_var($str,FILTER_SANITIZE_STRING);
}
// Sanitize a number
function safeNumber($num) {
	return filter_var($num,FILTER_SANITIZE_NUMBER_INT);
}
// Sanitize a floating point number
function safeFloat($num) {
	return filter_var($num,FILTER_SANITIZE_NUMBER_FLOAT);
}
// Sanitize an Email address
function safeEmail($email) {
	return filter_var($email,FILTER_SANITIZE_EMAIL);
}

// NEW Replacement Functions

// Sanitize a string
function sanitize_string($str) {
	return filter_var($str, FILTER_SANITIZE_STRING);
}

// Check if a variable is a boolean - be harsh and raise an error if it isn't!
function sanitize_boolean($bool) {
	
	if($bool !== true && $bool !== false && $bool !== 0 && $bool !== 1 && $bool !== "1" && $bool !== "0") {
		die("Input Type Error - Expected Boolean Found ".$bool." (".getType($bool).")");
	} else {
		return $bool;
	}
}

// Sanitize a blob upload
function sanitize_blob($str) {
	// Filter will not work here!
	return addslashes($str);
}

function sanitize_timestamp($ts) {
    if(!preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/', $ts)) {
        die("Unable to match timestamp - expected 'YYYY-MM-DD HH:mm:SS' found ".$ts);
    } else {
        return $ts;
    }
}

// Sanitize a number
function sanitize_number($num) {
	return filter_var($num, FILTER_SANITIZE_NUMBER_INT);
}

// Sanitize a floating point number
function sanitize_float($float) {
	return filter_var($float, FILTER_SANITIZE_NUMBER_FLOAT);
}

// Sanitize an Email address
function sanitize_email($email) {
	return filter_var($email, FILTER_SANITIZE_EMAIL);
}
?>