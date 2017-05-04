<?php function showFavoritePosts($arrayFavouriteBlogs, $parrayFavoriteBlogPosts) {

$output = "<h2>Recent posts from your favourite blogs</h2>";

if(getType($arrayFavouriteBlogs) != "array" || count($arrayFavouriteBlogs) == 0) {
	echo $output.showInfo("You have not got any favourite blogs, <a href='".CLIENT_ROOT_BLOGCMS."/explore'>Go Exploring</a>");
	return;
}

// Show list of favourite blogs
$output.= '<div id="favouriteblogs_sidemenu">'.PHP_EOL;
$output.= '<h4>Your Favourites</h4>'.PHP_EOL;

foreach($arrayFavouriteBlogs as $blog):
    $output.= '<a href="'.CLIENT_ROOT_BLOGCMS.'/blogs/'.$blog['id'].'">'.$blog['name'].'</a>'.PHP_EOL;
endforeach;

$output.= '</div>';
$output.= '<div id="favouriteblogs_posts">';

if(getType($parrayFavoriteBlogPosts) != "array" || count($parrayFavoriteBlogPosts) == 0) {
	echo $output.showInfo("None of your favourite bloggers have posted within the last 7 days!").'</div>';
    echo "<div><a href='".CLIENT_ROOT_BLOGCMS."/explore' class='action_button'>Explore Blogs</a></div><div style='clear:both;'></div>";
	return;
}

foreach($parrayFavoriteBlogPosts as $arrayPost):

	$linkpath = CLIENT_ROOT_BLOGCMS;
	$postcontent = trim(substr(wikiToHTML($arrayPost['content']), 0, 150));
	if(strlen($arrayPost['content']) > 150) $postcontent.= "...";
	$postdate = formatDate($arrayPost['timestamp'], "dS M");
	$posttime = formatTime($arrayPost['timestamp']);

$output.= <<<SUMMMARY

    <div class="recent-post">
        <p>
            &quot;<a href="{$linkpath}/blogs/{$arrayPost['blog_id']}/posts/{$arrayPost['link']}">{$arrayPost['title']}</a>&quot; - 
            <a href="{$linkpath}/blogs/{$arrayPost['blog_id']}">{$arrayPost['blog_name']}</a>
        </p>
        <p class="post_content">{$postcontent}</p>
        <p class="date">Posted on: {$postdate} at {$posttime}</p>
    </div>
	
SUMMMARY;

endforeach;

$output.= '</div>';

echo $output."<div class='push-right' style='clear:both;'><a href='".CLIENT_ROOT_BLOGCMS."/explore' class='action_button'>Explore Blogs</a></div>";
    
}
?>