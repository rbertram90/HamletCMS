<?php
	// Include Database Access
	include_once 'ajax_setup.inc.php';
	
	// Try to get post by name
	$blog_id = $_GET['blog_id'];
	$post_title = $_GET['post_title'];
	
	// Create the URL for the post
	$post_title = strtolower(str_replace(" ","-",safeString($post_title))); // Replace Spaces with dashes
	$post_title = str_replace("?","",$post_title); // Remove Question Marks
	$post_title = str_replace("!","",$post_title); // Remove Explanation Marks
	
	$mdl_posts = $mdl_posts->getPostByURL($post_title, $blog_id);
	
	// if(count($mdl_posts) > 0) {
	if($mdl_posts == false) echo "false";
	else echo "true";
?>