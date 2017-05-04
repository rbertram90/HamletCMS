<?php
function addView($postModel, $postid) {

	$arrayVisitors = $postModel->getViewsByPost($postid);
	$userip = $_SERVER['REMOTE_ADDR'];
	$countUpdated = False;
	
	foreach($arrayVisitors as $visitor) {
		if($userip == $visitor['userip']) {
			// already visited this page
			// $userviews = $visitor['viewcount'] + 1;
// echo "pre".$userviews;
			$postModel->incrementUserView($postid, $userip);
			$countUpdated = True;
			break;
		}
	}
	
	if(!$countUpdated) {
		// New Visitor
		$postModel->recordUserView($postid, $userip);
	}
}
?>