<?php
// notes - need to check we are not breaking an html tag
function viewSummary($fullcontent, $charactersToShow='all', $openTag='<', $closeTag='>') {
	
	if($charactersToShow !== "all") {
	
		// Number of characters has been limited
		$trimmedContent = substr($fullcontent, 0, $charactersToShow);
		$lastOpeningTag = strrpos($trimmedContent, $openTag);
		
		if($lastOpeningTag !== false) {
		
			// There has been a tag started (we thinks)
			$lastClosingTag = strrpos($trimmedContent, $closeTag);
			
			if($lastClosingTag === false || $lastOpeningTag > $lastClosingTag) {
				
				// Believe there is still an open tag
				$nextClosingTag = strpos($fullcontent, $closeTag, $lastOpeningTag + 1);
				$nextOpeningTag = strpos($fullcontent, $openTag, $lastOpeningTag + 1);
				
				if($nextOpeningTag !== false && $nextClosingTag !== false) {
					if($nextClosingTag < $nextOpeningTag) {
						$charactersToShow = $nextClosingTag + 1;
					}
				}
				elseif($nextClosingTag !== false) {
					// Choose to end the substr after the tag has finished
					$charactersToShow = $nextClosingTag + 1;
				}
			}
		}
		// Reapply Limit to X characters
		$trimmedContent = substr($fullcontent, 0, $charactersToShow);
		
		// Add continuation marks if actual length is more than summary
		if(strlen($fullcontent) > $charactersToShow && $charactersToShow > 0) $trimmedContent.= "...";
	}
	else {
		$trimmedContent = $fullcontent;
	}
	
	// Remove Whitespace and return answer
	return trim($trimmedContent);
}