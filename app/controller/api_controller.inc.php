<?php
namespace rbwebdesigns\blogcms;

use rbwebdesigns\core\Sanitize;
use rbwebdesigns\core\JSONhelper;

/**
 * ApiController
 * 
 * @author R Bertram <ricky@rbwebdesigns.co.uk>
 * 
 * @todo decide on where data is coming from for cms vs public facing api
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
    /**
     * @var \rbwebdesigns\core\Request
     */
    protected $request;
    /**
     * @var \rbwebdesigns\core\Response;
     */
    protected $response;
    
    public function __construct(&$request, &$response)
    {
        $this->modelBlogs = BlogCMS::model('\rbwebdesigns\blogcms\model\Blogs');
        $this->modelPosts = BlogCMS::model('\rbwebdesigns\blogcms\model\Posts');

        $this->request = $request;
        $this->response = $response;
    }
    
    /**
     * Handles /api
     * Currently nothing implemented - would be helpful to redirect to some
     * sort of documentation page
     */
    public function defaultAction()
    {
        $result = [
            'error' => 'Unknown API method'
        ];
        $this->response->addHeader('Content-Type', 'application/json');
        $this->response->code(404);
        $this->response->setBody(JSONhelper::arrayToJSON($result));
        $this->response->writeBody();
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
    public function posts()
    {
        $blogID = $this->request->getInt('blogID', -1);
        $start  = $this->request->getInt('start', 1);
        $limit  = $this->request->getInt('limit', 10);
        $sort   = $this->request->getString('sort', 'name ASC');

        $showDrafts = $this->request->getString('showdrafts', 'true');
        $showDrafts = ($showDrafts == 'true') ? 1 : 0;

        $showScheduled = $this->request->getString('showscheduled', 'true');
        $showScheduled = ($showScheduled == 'true') ? 1 : 0;

        if(!$blog = $this->modelBlogs->getBlogById($blogID)) {
            die("{ 'error': 'Unable to find blog' }");
        }
        
        $result = [
            'blog'      => $blog,
            'postcount' => $this->modelPosts->countPostsOnBlog($blogID, true, true),
            'posts'     => $this->modelPosts->getPostsByBlog($blogID, $start, $limit, $showDrafts, $showScheduled, $sort),
        ];

        $this->response->addHeader('Content-Type', 'application/json');
        $this->response->setBody(JSONhelper::arrayToJSON($result));
        $this->response->writeBody();
    }


    public function blog()
    {
        $blogID = $this->request->getInt('blogID', -1);

        if(!$blog = $this->modelBlogs->getBlogById($blogID)) {
            die("{ 'error': 'Unable to find blog' }");
        }

        $this->response->addHeader('Content-Type', 'application/json');
        $this->response->setBody(JSONhelper::arrayToJSON($blog));
        $this->response->writeBody();
    }
    
    public function blogCount()
    {
        $blogCount = $this->modelBlogs->count();
        return false;
    }

    public function countByLetter()
    {
        $counts = $this->modelBlogs->countBlogsByLetter();

        $this->response->addHeader('Content-Type', 'application/json');
        $this->response->setBody(JSONhelper::arrayToJSON($counts));
        $this->response->writeBody();
    }
    
    public function blogsByLetter()
    {
        $letter = $this->request->getString('letter', false);

        if ($letter) {
            $blogs = $this->modelBlogs->getBlogsByLetter($letter);
        }
        else {
            $blogs = [];
        }

        

        $this->response->addHeader('Content-Type', 'application/json');
        $this->response->setBody(JSONhelper::arrayToJSON($blogs));
        $this->response->writeBody();
    }

    
}
