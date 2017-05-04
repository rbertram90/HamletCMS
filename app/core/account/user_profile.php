<?php
function viewFullUserProfile($user) {

	$clientroot = CLIENT_ROOT;
	$clientrootblogcms = CLIENT_ROOT_BLOGCMS;
	if($user['admin'] == 1) $admin = " - Site Admin";
	else $admin = "";
	
	$blogtable = showUsersBlogs($user);

echo <<<EOD
	<style>
		#user-general-info th {
			width:140px;
			text-align:left;
		}
		.profile-photo {
			margin-bottom:10px;
		}
	</style>

	<div style="width:60%; margin:0 auto;">

		<div class="project_site">
			<h1>{$user['name']} {$user['surname']} ({$user['username']}){$admin}</h1>
			
			<img src="{$clientroot}/projects/sample3/profile_images/thumbs/{$user['profile_picture']}" alt="[Profile Photo]" class="profile-photo" />
			
			<h2>General</h2>
			<table id="user-general-info" cellpadding="10" cellspacing="0">
				<tr><th>Gender</th><td>{$user['gender']}</td></tr>
				<tr><th>Date of Birth</th><td>{$user['dob']}</td></tr>
				<tr><th>Location</th><td>{$user['location']}</td></tr>
				<tr><th>Bio</th><td>{$user['description']}</td></tr>
			</table>
			
			<h2>{$user['name']}'s Blogs</h2>
			{$blogtable}
			<div class="push-right">
				<a href="{$clientrootblogcms}">Go to Blog CMS</a>
			</div>
			
		</div>
	
	</div>
EOD;

}

function showUsersBlogs($user) {

	// Gain access to the blog_cms data
	require_once SERVER_ROOT.'/app/envsetup.inc.php';
	$modelBlogs = new rbwebdesigns\blogcms\ClsBlog($cms_db);
	
	$output = "";
	$blogroot = CLIENT_ROOT_BLOGCMS;
	$blogs = $modelBlogs->getBlogsByUser($user['id']);
	
	foreach($blogs as $blog):
		$output.= "<li><a href='{$blogroot}/blogs/{$blog["id"]}'>{$blog["name"]}</a></li>";
	endforeach;

	return '<ul>'.$output.'</ul>';
}
?>