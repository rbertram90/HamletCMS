<?php
class EmailHelper {

	public function __construct() {
		
	}
	
	public function safeEmail(&$psField) {
	    filter_var($psField, FILTER_SANITIZE_EMAIL);
	    if(filter_var($psField, FILTER_VALIDATE_EMAIL)) return true;
	    else return false;
	}
	
	public function send($psRecipient, $psSender, $psSubject, $psMessage) {
		
		// Santize Email Addresses
		$this->safeEmail($psRecipient);
		$this->safeEmail($psSender);
		
		// Check that both were valid
		if(!$psRecipient || !$psSender) return 'Unable to send email - Missing email address';
		
		// Sanitize the content
		$psSubject = filter_var($psSubject, FILTER_SANITIZE_STRING);
		$psMessage = filter_var($psMessage, FILTER_SANITIZE_STRING);
		
		// Send the email
		mail($psSender, 'Subject: '.$psSubject, $psMessage, 'From: '.$psRecipient);
		
		return 'Message sent successfully';
	}
}
?>