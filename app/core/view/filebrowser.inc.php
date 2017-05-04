<?php
/*******************************
   RB File Browser - 2013
*******************************/

function showJpgDirectory($path, $allowParent) {

	// Check folder exists
	if (!is_dir($path)) {
		echo "Unable to find directory!";
		return false;
	}
	
    // Try and open the directory
	if ($handle = opendir($path)) {

		// loop over the directory
		while (false !== ($entry = readdir($handle))) {
		
			switch(filetype($path.'/'.$entry)):
				case "file":
					// Get the file extention
					$lastbullet = strrchr($entry,'.');
					$ext = strtolower(substr($lastbullet,1));
					
					// Only show JPG images
					if ($ext == "jpg") {
						echo "<img src='/rbwebdesigns/wiki/imagelib/$entry' class='libicon' />$entry<br/>";
					}
					
					break;
					
				case "dir":
                    if(!$allowParent && ($entry == "." || $entry == "..")) {
                        // Don't display link as we don't want to go to higher dir   
                    } else {
					   echo "<a href='#' onclick='showDirectory($entry)'>$entry</a><br/>";
                    }
					break;
					
			endswitch;
		}
		closedir($handle);
	}
}

// Make new folder
// mkdir('/test1/test2', 0777, true);
?>