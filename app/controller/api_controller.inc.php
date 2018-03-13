<?php
namespace rbwebdesigns\blogcms;

use rbwebdesigns\core\Sanitize;
use rbwebdesigns\core\JSONhelper;

/**
 * ApiController
 */
class ApiController extends GenericController
{
    /**
     * @var \rbwebdesigns\blogcms\model\Blogs
     */
    protected $modelBlogs;
    /**
     * @var \rbwebdesigns\blogcms\model\Blogs
     */
    protected $modelPosts;
    
    public function __construct()
    {
        $this->modelBlogs = BlogCMS::model('\rbwebdesigns\blogcms\model\Blogs');
        $this->modelPosts = BlogCMS::model('\rbwebdesigns\blogcms\model\Posts');
    }
    
    /**
     * Handles /api
     * Currently nothing implemented - would be helpful to redirect to some
     * sort of documentation page
     */
    public function defaultAction(&$request, &$response)
    {
        return $response->redirect('/', 'Invalid request', 'error');
    }

    /**
     * Get data for posts in JSON format
     * Handles /api/posts
     *
     * Request Parameters:
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
    public function posts(&$request, &$response)
    {
        $blogID = $request->getInt('blogID', -1);
        $start  = $request->getInt('start', 1);
        $limit  = $request->getInt('limit', 10);
        $sort   = $request->getString('sort', 'name ASC');

        $showDrafts = $request->getString('showdrafts', 'true');
        $showDrafts = ($showDrafts == 'true') ? 1 : 0;

        $showScheduled = $request->getString('showscheduled', 'true');
        $showScheduled = ($showScheduled == 'true') ? 1 : 0;

        if(!$blog = $this->modelBlogs->getBlogById($blogID)) {
            die('Error: Unable to find blog - ' . $blogID);
        }
        
        $result = [
            'blog'      => $blog,
            'postcount' => $this->modelPosts->countPostsOnBlog($blogID, true, true),
            'posts'     => $this->modelPosts->getPostsByBlog($blogID, $start, $limit, $showDrafts, $showScheduled, $sort),
        ];

        header('Content-Type: application/json');
        echo JSONhelper::ArrayToJSON($result);
    }
    
}
