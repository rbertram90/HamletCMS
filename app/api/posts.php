<?php
namespace rbwebdesigns\blogcms;
use rbwebdesigns\core\Sanitize;
use rbwebdesigns\core\JSONhelper;

/**
 * Get data for posts in JSON format
 *
 * /api/posts?blogID=350932458034&start=40&limit=20
 *
 * GET Parameters:
 *  blogID (required) - ID of the blog e.g. 1983749328
 *  start (optional) - first post to start from
 *  limit (optional) - number of posts to get
 *  sort (optional) - ordering: 
 *    timestamp ASC/DESC
 *    title ASC/DESC
 *    author_id ASC/DESC
 *    hits ASC/DESC
 *    uniqueviews ASC/DESC
 *    numcomments ASC/DESC
 *  showdrafts (optional) - include draft posts (true / false)
 *  showscheduled (optional) - include scheduled posts (true / false)
 */

// Request Parameters
$blogID = isset($_GET['blogID']) ? Sanitize::int($_GET['blogID']) : die('Error: Required argument blogID not supplied');

$start = isset($_GET['start']) ? Sanitize::int($_GET['start']) : 1;

$limit = isset($_GET['limit']) ? Sanitize::int($_GET['limit']) : 10;

$sort = isset($_GET['sort']) ? Sanitize::string($_GET['sort']) : 'name ASC';

// include drafts
$showDrafts = isset($_GET['showdrafts']) ? Sanitize::string($_GET['showdrafts']) : '1';    
$showDrafts = ($showDrafts == 'true') ? 1 : 0;

// include scheduled posts
$showScheduled = isset($_GET['showscheduled']) ? Sanitize::string($_GET['showscheduled']) : '1';    
$showScheduled = ($showScheduled == 'true') ? 1 : 0;

// Check blog exists
if(!$blog = $modelBlogs->getBlogById($blogID)) die('Error: Unable to find blog - ' . $blogID);

$result = array(
    'blog' => $blog,
    'postcount' => $modelPosts->countPostsOnBlog($blogID, true, true)
);

// Get all posts
$result['posts'] = $modelPosts->getPostsByBlog($blogID, $start, $limit, $showDrafts, $showScheduled, $sort);

// Header for JSONP
header('Content-Type: application/json');

// Output as JSON
echo JSONhelper::ArrayToJSON($result);