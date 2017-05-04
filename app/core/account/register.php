	<?php
		// Just starting registration
		if(!isset($_POST['fld_submit_registration'])) {
		
			// Include Initial Registration form
			include_once("inc/view/signup-stepone.inc.php");
		}
		else {
			// First page has been submitted
			
			// Require registration processing script
			require_once("inc/ctrl/ctrl-register.php");
			
			// Include next step
			include_once("inc/view/signup-steptwo.inc.php");
		}
	?>