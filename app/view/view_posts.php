<?php
/*********************************************************************
  view_posts.php
  View blog posts either as the blog homepage or an individual post
*********************************************************************/

use \Michelf\Markdown;
use rbwebdesigns\core\Pagination;

require_once SERVER_ROOT.'/app/view/view_post_summary.php';


// View the full content of a single post
function viewSinglePost($arrayPost, $arrayPostSettings, $prevPost="", $nextPost="") {
    
    echo '<div class="post">';
    
    // Default Timestamps - should really be global constants!
    $dateformat = "Y-m-d";
    $timeformat = "H:i:s";

    if(is_array($arrayPostSettings) && array_key_exists('posts', $arrayPostSettings)) {
        $dateformat = $arrayPostSettings['posts']['dateformat'];
        $timeformat = $arrayPostSettings['posts']['timeformat'];
        $showtags     = isset($arrayPostSettings['posts']['showtags'])     ? $arrayPostSettings['posts']['showtags']     : 1;
        $datelocation = isset($arrayPostSettings['posts']['timelocation']) ? $arrayPostSettings['posts']['timelocation'] : 'footer';
    } else {
        $showtags = 1;
        $datelocation = 'footer';
    }
    
    if(getType($arrayPostSettings) == 'array' && array_key_exists('posts', $arrayPostSettings)) {
        echo showTimeDate($arrayPost['timestamp'], $arrayPostSettings['posts'], 'title');
    }
    else {
        echo showTimeDate($arrayPost['timestamp'], array(), 'title');
    }
    ?>

    <h1 class="post-title"><?=$arrayPost['title']?></h1>

    <!-- Note we won't always want to use wiki language! need to either ask user or 
    programmatically determine if the post uses wiki -->
    <div class="post-content"><?php
        
        if($arrayPost['type'] == 'video') {
            switch($arrayPost['videosource']) {
                case 'youtube':
                    echo '<iframe width="100%" style="max-width:560px;" height="315" src="//www.youtube.com/embed/'.$arrayPost['videoid'].'" frameborder="0" allowfullscreen></iframe>';
                    break;
                case 'vimeo':
                    echo '<iframe src="//player.vimeo.com/video/'.$arrayPost['videoid'].'?title=0&amp;byline=0&amp;portrait=0&amp;color=fafafa" width="560" height="315" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
                    break;
            }
        }
        
        if($arrayPost['type'] == 'gallery')
        {
            $gallery = "<div id='galleria_{$arrayPost['id']}'>";
            
            $images = explode(',', $arrayPost['gallery_imagelist']);
            
            foreach($images as $path)
            {
                if(strlen($path) > 0)
                {
                    $gallery.= '<img src="'.$path.'" />';
                }
            }
            
            $gallery.= '</div>';
            $gallery.= '<style>#galleria_'.$arrayPost['id'].'{ width: 100%; height: 400px; background: #000 }</style>';
            $gallery.= '<script>Galleria.run("#galleria_'.$arrayPost['id'].'");</script>';
            
            echo $gallery;
        }
        
        echo Markdown::defaultTransform($arrayPost['content']);
    ?></div>
    
    <div class="post-footer">
        <!-- Post Date -->
        <?php
        if(getType($arrayPostSettings) == 'array' && array_key_exists('posts', $arrayPostSettings)) {
            echo showTimeDate($arrayPost['timestamp'], $arrayPostSettings['posts'], 'footer');
        }
        else {
            echo showTimeDate($arrayPost['timestamp'], array(), 'footer');
        }
        ?>
        
        <!-- Post Tags -->
        <?php if(strlen($arrayPost['tags']) > 0 && $showtags != '0'): ?>
            <p class="post-tags">Tags: <?=showTags($arrayPost['blog_id'], $arrayPost['tags'])?></p>
        <?php endif; ?>
        
        <!-- Add / Edit Options -->
        <?php if(isset($_SESSION['userid']) && $arrayPost['author_id'] == $_SESSION['userid']): ?>
            <a href="/posts/<?=$arrayPost['blog_id']?>/edit/<?=$arrayPost['id']?>">Edit</a> | 
            <a href="/posts/<?=$arrayPost['blog_id']?>/delete/<?=$arrayPost['id']?>" onclick='return confirm(\"Are you sure?\");'>Delete</a>
        <?php endif; ?>

<?php
        if(defined('EXTERNAL_DOMAIN') && EXTERNAL_DOMAIN == 1)
        {
            $blogdir = '';
        }
        else
        {
            $blogdir = '/blogs/'.$arrayPost['blog_id'];
        }

        if(getType($nextPost) == "array")
        {
            echo '<a href="'.$blogdir.'/posts/'.$nextPost['link'].'" style="float:right;" class="next-post-link"><span>Next Post: </span>'.$nextPost['title'],' &gt;</a>';
        }

        if(getType($prevPost) == "array")
        {
            echo '<a href="'.$blogdir.'/posts/'.$prevPost['link'].'" class="previous-post-link"><span>&lt; Previous Post: </span>'.$prevPost['title'],'</a>';
        }
        
        echo '</div></div>';
}


function getNumPostsToView($pobjPostSettings) {
    // Set number of posts to get
    $postsperpage = 5;
    
    if(is_array($pobjPostSettings)) {
        if(array_key_exists('posts', $pobjPostSettings)) {
            if(array_key_exists('postsperpage', $pobjPostSettings['posts'])) $postsperpage = $pobjPostSettings['posts']['postsperpage'];
        }
    }
    return $postsperpage;
}


/**
 * @param blogid <int> blog id
 * @param tags <string> comma-seperated tag list
 * @return <string> html to display
 */
function showTags($blogid, $tags) {
    
    // Split the string by comma
    $arrayTags = explode(',', $tags);
    
    // Default Output
    $output = "";
    
    // Loop through each tag generating the html
    foreach($arrayTags as $tag) {
        $caption = str_replace("+", " ", $tag);
        $output.= '<a href="/blogs/'.$blogid.'/tags/'.$tag.'" class="tag">'.$caption.'</a>';
    }
    
    return $output;
}


// Show Date and/or time - this has been seperate into a function
// because there are lots of different options!

// Settings - options submitted in post settings screen
// Location - where are we showing this date - set within this file

// e.g $html = showTimeDate(array(
//  'dateformat' => 'Y-m-d',
//  'timeformat' => 'H:i:s'
//  ), 'footer');

function showTimeDate($timestamp, $postSettings, $location) {
    
    $res = "";
    
    // Get Values
    if(getType($postSettings) == 'array') {
        $dateformat    = array_key_exists('dateformat', $postSettings)    ? $postSettings['dateformat']    : 'Y-m-d';
        $timeformat    = array_key_exists('timeformat', $postSettings)    ? $postSettings['timeformat']    : 'H:i:s';
        $timelocation  = array_key_exists('timelocation', $postSettings)  ? $postSettings['timelocation']  : 'footer';
        $datelocation  = array_key_exists('datelocation', $postSettings)  ? $postSettings['datelocation']  : 'footer';
        $dateprefix    = array_key_exists('dateprefix', $postSettings)    ? $postSettings['dateprefix']    : 'Posted on: ';
        $dateseperator = array_key_exists('dateseperator', $postSettings) ? $postSettings['dateseperator'] : ' at ';
    } else {
        $dateformat    = 'Y-m-d';
        $timeformat    = 'H:i:s';
        $timelocation  = 'footer';
        $datelocation  = 'footer';
        $dateprefix    = 'Posted on: ';
        $dateseperator = ' at ';
    }
    
    if($datelocation == $location) {
        // Show Date
        $res.= $dateprefix;
        $res.= date($dateformat, strtotime($timestamp));
    }
    
    if($timelocation == $location) {
        // Show Time
        $res.= $dateseperator;
        $res.= date($timeformat, strtotime($timestamp));
    }
    
    if(strlen($res) > 0) {
        // Return date nicely wrapped in HTML
        return '<p class="post-date">'.$res.'</p>';
    }
    else {
        // Return nothing
        return '';
    }
}

// View a intro to multiple posts (standard blog view)
function viewMultiplePosts($arrayPosts, $blogid, $arrayPostSettings, $pTotalPostsOnBlog, $pCurrentPage) {

    // Work out pagination
    $numPostsToShow = getNumPostsToView($arrayPostSettings);
    
    if(is_array($arrayPostSettings) && array_key_exists('posts', $arrayPostSettings))
    {
        $postSettings = $arrayPostSettings['posts'];
    }
    else
    {
        $postSettings = array();
    }
    
    // Set formatting
    $showtags        = array_key_exists('showtags', $postSettings)          ? $postSettings['showtags']          : 1;
    $shownumcomments = array_key_exists('shownumcomments', $postSettings)   ? $postSettings['shownumcomments']   : 1;
    $showsocialicons = array_key_exists('showsocialicons', $postSettings)   ? $postSettings['showsocialicons']   : 1;
    $summarylength   = array_key_exists('postsummarylength', $postSettings) ? $postSettings['postsummarylength'] : 150;
        
    // We are shorten URLs on blogs on external domains using htaccess
    // and a specific link path - this has been an afterthought so could
    // be thought about at a higher level?
        
    if(defined('EXTERNAL_DOMAIN') && EXTERNAL_DOMAIN == 1)
    {
        $blogdir = '';
    }
    else
    {
        $blogdir = '/blogs/'.$blogid;
    }    
    
    // Loop through posts
    foreach($arrayPosts as $lobjPost):
        
        $trimmedContent = Markdown::defaultTransform($lobjPost['content']);
    
        if($lobjPost['type'] == 'video')
        {
            switch($lobjPost['videosource'])
            {
                case 'youtube':
                    $trimmedContent = '<iframe width="100%" height="400px" src="//www.youtube.com/embed/'.$lobjPost['videoid'].'" frameborder="0" allowfullscreen></iframe>'.$trimmedContent;
                    break;
                    
                case 'vimeo':
                    $trimmedContent = '<iframe src="//player.vimeo.com/video/'.$lobjPost['videoid'].'?title=0&amp;byline=0&amp;portrait=0&amp;color=fafafa" width="560" height="315" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>'.$trimmedContent;
                    break;
            }
        }
        
        if($lobjPost['type'] == 'gallery')
        {
            $gallery = "<div id='galleria_{$lobjPost['id']}'>";
            
            $images = explode(',', $lobjPost['gallery_imagelist']);
            
            foreach($images as $path)
            {
                if(strlen($path) > 0)
                {
                    $gallery.= '<img src="'.$path.'" />';
                }
            }
            
            $gallery.= '</div>';
            $gallery.= '<style>#galleria_'.$lobjPost['id'].'{ width: 100%; height: 400px; background: #000 }</style>';
            $gallery.= '<script>Galleria.run("#galleria_'.$lobjPost['id'].'");</script>';
            
            $trimmedContent = $gallery.$trimmedContent;
            
            // increase the length
            $summarylength += strlen($gallery);
        }
    
        $postSummary = viewSummary($trimmedContent, $summarylength, '<', '>');
        $posttags = strlen($lobjPost['tags']) > 0 ? showTags($blogid, $lobjPost['tags']) : "<i>Nothing</i>";
?>

    <div class="post">
        
        <!-- Post Date -->
        <?php echo showTimeDate($lobjPost['timestamp'], $postSettings, 'title'); ?>
        
        <h3 class="post-title"><a href="<?=$blogdir?>/posts/<?=$lobjPost['link']?>"><?=$lobjPost['title']?></a></h3>
        
        <div class="post-content"><?=$postSummary?></div>
        
        <div class="post-footer">
            <!-- Post Date -->
            <?php echo showTimeDate($lobjPost['timestamp'], $postSettings, 'footer'); ?>
            
            <!-- Post Tags -->
            <?php if(strlen($lobjPost['tags']) > 0 && $showtags != '0'): ?>
                <p class="post-tags">Tags: <?=$posttags?></p>
            <?php endif; ?>
            
            <?php if($shownumcomments != 0): ?>
            <p class='post-comment-count'><?=$lobjPost["numcomments"]?> comments</p>
            <?php endif; ?>
                        
            <!-- Add / Edit Options -->
            <?php if(isset($_SESSION['userid']) && $lobjPost['author_id'] == $_SESSION['userid']): ?>
            <p class='post-actions'>
                <a href="/posts/<?=$lobjPost['blog_id']?>/edit/<?=$lobjPost['id']?>">Edit</a> | 
                <a href="/posts/<?=$lobjPost['blog_id']?>/delete/<?=$lobjPost['id']?>" onclick='return confirm(\"Are you sure?\");'>Delete</a>
            </p>
            <?php endif; ?>
            
            <!-- Social Media -->
            <?php if($showsocialicons != 0): ?>
            <?php
                $encodedTitle = rawurlencode($lobjPost['title']);
                $encodedUrl   = rawurlencode($blogdir.'/posts/'.$lobjPost['link']);
                $unencodedUrl = $blogdir.'/posts/'.$lobjPost['link'];
            ?>
            <style>.social-icons img { width:30px; height:30px; }</style>
            <div class="social-icons">
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?=$encodedUrl?>" onclick="window.open(this.href, 'height=600,width=400'); return false;"><img src="/resources/icons/social/facebook256.png" /></a>
                <a href="https://twitter.com/intent/tweet?url=<?=$encodedUrl?>&text=<?=$encodedTitle?>" target="_blank"><img src="/resources/icons/social/twitter256.png" /></a>
                <a href="https://plus.google.com/share?url=<?=$unencodedUrl?>" target="_blank"><img src="/resources/icons/social/googleplus256.png" /></a>
                <a href="mailto:?subject=<?=$encodedTitle?>&amp;body=<?=$encodedUrl?>"><img src="/resources/icons/social/email256.png" /></a>
            </div>
            <?php endif; ?>
        </div>
    </div>
        
    <?php endforeach; ?>

<?php

    // Show Pagination Menu
    $numPages = ceil($pTotalPostsOnBlog / $numPostsToShow);
    $paginator = new Pagination();
    echo $paginator->showPagination($numPages, $pCurrentPage);
}


/**
 * View Comments
 * Prints out all comments passed in
 * @param $comments array of comments
 */
function viewComments($comments) {

    echo "<div class='comments'><h2>Comments</h2>";
    
    if(count($comments) == 0)
    {
        echo showInfo("No comments have been made on this post")."</div>";
        return;
    }
    
    foreach($comments as $comment):
    
        // See if we can get a known user?
        if($comment['user_id'] !== 0)
        {
            $modelUers = $GLOBALS['models']['users'];
            $user = $modelUers->getUserById($comment['user_id']);
            $comment['name'] = $user['username'];
            $comment['fullname'] = $user['name']." ".$user['surname'];
        }
?>
        <p><b><?=$comment['name']?> (<?=$comment['fullname']?>)</b></p>
        <p><i>&quot;<?=$comment['message']?>&quot;</i></p>
        
<?php endforeach;
    
    echo "</div>";
}


/**
 * HTML form for a adding a comment to a blog post
 * @param $blog array containing data about blog
 * @param $post array containing data about post
 */
function commentForm($blog, $post)
{
    if(!USER_AUTHENTICATED)
    {
        echo showInfo("You need to be logged in to add comments");
        return;
    }
?>
    <div class="comment-form">
        <form action="/blogs/<?=$blog['id']?>/posts/<?=$post['id']?>/addcomment" method="POST">
            
            <label for="fld_comment">Comment</label>
            <textarea name="fld_comment"></textarea>

            <div class="push-right">
                <input type="submit" name="fld_submitcomment" value="Add Comment" />
            </div>
        </form>
    </div>
<?php
}
?>