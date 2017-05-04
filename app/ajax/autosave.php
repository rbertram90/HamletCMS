<?php
/**
	Autosave function
	
	Called via ajax every 5 seconds
	if it is a new post then a draft is created in the posts table and a autosave is created in the autosaves table
**/

namespace rbwebdesigns\blogcms;
use rbwebdesigns;

$modelPosts = new ClsPost($this->db);
$updateDB = $modelPosts->autosavePost();

// return result as JSON
if($updateDB === false) {
	echo json_encode(array(
		'status' => 'failed',
		'message' => 'Could not run autosave - DB Update Error'
	));
	
} else if($updateDB > 0 && $updateDB !== sanitize_number($_POST['fld_postid'])) {
	echo json_encode(array(
		'status' => 'success',
		'message' => 'Post last saved at '.date('Y-m-d H:i:s'),
		'newpostid' => $updateDB
	));
	
} else {
	echo json_encode(array(
		'status' => 'success',
		'message' => 'Post last saved at '.date('Y-m-d H:i:s')
	));
}
?>