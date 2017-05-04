<?php
class validate {

	public function __construct() {
	
	}

	// what do we need from a validation class
	public function validateEmail($email) {
		if(isEmail($email)) return true;
	}
	
	public function validateInterger($num) {
		if(is_numeric($num)) return true;
	}
	
	public function validatePostcode($postcode) {
		if(is_valid_uk_postcode($postcode)) return true;
	}
	
	public function validatePhoneNumber($phoneNumber) {
	
	}
}
?>