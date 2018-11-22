<?php
namespace rbwebdesigns\blogcms\BlogPosts\controller;

use rbwebdesigns\blogcms\GenericController;
use rbwebdesigns\blogcms\BlogCMS;
use rbwebdesigns\core\JSONHelper;

/**
 * Class PostsAPI
 * 
 * @author R Bertram <ricky@rbwebdesigns.co.uk>
 */
class PostsAPI extends GenericController
{

    /**
     * @var \rbwebdesigns\blogcms\BlogPosts\model\Posts
     */
    protected $model;
    /**
     * @var \rbwebdesigns\blogcms\Blog\model\Blogs
     */
    protected $modelBlogs;
    
    /**
     * Posts controller constructor
     */
    public function __construct()
    {
        $this->model = BlogCMS::model('\rbwebdesigns\blogcms\BlogPosts\model\Posts');
        $this->modelBlogs = BlogCMS::model('\rbwebdesigns\blogcms\Blog\model\Blogs');

        parent::__construct();

        /*
        if (!BlogCMS::$blogID) {
            $postID = $this->request->getUrlParameter(1);
            $this->post = $this->model->getPostById($postID);
            BlogCMS::$blogID = $this->post['blog_id'];
        }

        $this->blog = BlogCMS::getActiveBlog();
        */
    }

    /**
     * Create a new post
     */
    public function create()
    {
        // Already validated this as default API request
        $blogID = $this->request->getInt('blogID', false);
        $blog = $this->modelBlogs->getBlogById($blogID);

        $posttime = strtotime($this->request->getString('date'));
        
        if (checkdate(date("m", $posttime), date("d", $posttime), date("Y", $posttime))) {
            $postdate = date("Y-m-d H:i:00", $posttime);
        }
        else {
            $postdate = date("Y-m-d H:i:00");
        }
        
        $newPost = [
            'title'           => $this->request->getString('title'),
            'content'         => $this->request->get('content'),
            'summary'         => $this->request->getString('summary'),
            'tags'            => $this->request->getString('tags'),
            'teaser_image'    => $this->request->getString('teaserimage'),
            'blog_id'         => $this->blog->id,
            'draft'           => $this->request->getInt('draft'),
            'allowcomments'   => $this->request->getInt('comments'),
            'type'            => $this->request->getString('type'),
            'initialautosave' => 0,
            'timestamp'       => $postdate
        ];

        // Check title provided
        if (strlen($newPost['title']) == 0) {
            $this->response->setBody('{ "success": "false", "errorMessage": "Please provide a title" }');
            $this->response->code(400);
            return;
        }

        // Validate unique title
        $url = $this->model->createSafePostUrl($newPost['title']);
        if ($post = $this->model->getPostByURL($url, $this->blog->id)) {
            $this->response->setBody('{ "success": "false", "errorMessage": "Title is already in use" }');
            $this->response->code(400);
            return;
        }
        
        // Process custom fields for different post types
        BlogCMS::runHook('onBeforePostSaved', ['post' => &$newPost]);

        if (!$this->model->createPost($newPost)) {
            $this->response->setBody('{ "success": "false", "errorMessage": "Error creating post" }');
            $this->response->code(500);
            return;
        }

        // Get the post created - with ID and URL
        $post = $this->model->getPostByURL($url, $this->blog->id);

        BlogCMS::runHook('onPostCreated', ['post' => $post]);

        // todo - add new post ID
        $this->response->setBody('{ "success": "true", "post": ' . json_encode($post) .' }');
    }

    /**
     * Edit an existing post
     */
    public function edit()
    {
        $postID = $this->request->getInt('postID');
        $blogID = $this->request->getInt('blogID');

        if (!$post = $this->model->getPostById($postID)) {
            $this->response->setBody('{ "success": false, "errorMessage": "Post not found" }');
            $this->response->code(406);
            return;
        }

        // We've already verified that the user has access to post for this
        // blog so check the blog ID listed for this post is a match
        if ($post['blog_id'] != $blogID) {
            $this->response->setBody('{ "success": false, "errorMessage": "Blog ID mismatch" }');
            $this->response->code(406);
            return;
        }

        // Check & Format date
        $posttime = strtotime($this->request->getString('date'));

        if (checkdate(date("m", $posttime), date("d", $posttime), date("Y", $posttime))) {
            $postdate = date("Y-m-d H:i:00", $posttime);
        }
        else {
            $postdate = $post['timestamp']; // Keep to original
        }
        
        $updates = [
            'id'              => $postID,
            'type'            => $post['type'],
            'title'           => $this->request->getString('title'),
            'summary'         => $this->request->getString('summary'),
            'content'         => $this->request->get('content'),
            'tags'            => $this->request->getString('tags'),
            'teaser_image'    => $this->request->getString('teaserImage'),
            'draft'           => $this->request->getInt('draft'),
            'allowcomments'   => $this->request->getInt('comments'),
            'initialautosave' => 0,
            'timestamp'       => $postdate
        ];

        // Check title provided
        if (strlen($updates['title']) == 0) {
            $this->response->setBody('{ "success": "false", "errorMessage": "Please provide a title" }');
            $this->response->code(400);
            return;
        }

        // Validate unique title
        $url = $this->model->createSafePostUrl($updates['title']);
        if ($this->model->count(['blog_id' => $blogID, 'link' => $url]) > 0) {

            $matchingPost = $this->model->getPostByURL($url, $blogID);
            if ($matchingPost['id'] != $postID) {
                $this->response->setBody('{ "success": "false", "errorMessage": "Title is already in use" }');
                $this->response->code(400);
                return;
            }
        }

        // Process custom fields for different post types
        BlogCMS::runHook('onBeforePostSaved', ['post' => &$updates]);

        $this->model->updatePost($post['id'], $updates);
        $this->model->removeAutosave($post['id']);
        
        // Re-fetch post data - will have updated URL alias
        $post = $this->model->getPostByURL($url, $blogID);

        BlogCMS::runHook('onPostUpdated', ['post' => array_merge($post, $updates)]);

        $this->response->setBody('{ "success": true, "post": '. json_encode($post) .' }');
    }
    
    /**
     * Handles /api/posts/delete/<postID>
     * 
     * @todo make sure there are no pages with this post ID
     */
    public function delete()
    {
        $postID = $this->request->getInt('postID');
        $blogID = $this->request->getInt('blogID');
        $post = $this->model->getPostById($postID);

        if (!$post || !isset($post['blog_id']) || $blogID != $post['blog_id']) {
            $this->response->setBody('{ "success": false, "errorMessage": "Blog ID Mismatch" }');
            $this->response->code(400);
            return;
        }

        if($this->model->delete(['id' => $post['id']]) && $this->model->removeAutosave($post['id'])) {
            BlogCMS::runHook('onPostDeleted', ['post' => $post]);
            $this->response->setBody('{ "success": true }');
        }
        else {
            $this->response->setBody('{ "success": false, "errorMessage": "Error deleting post" }');
            $this->response->code(500);
        }
    }
    
    /**
     * Handles /api/posts/autosave/<postID>
     */
    public function autosave()
    {
        $postID = $this->request->getInt('postID');

        $data = [
            'title'         => $this->request->getString('title'),
            'summary'       => $this->request->getString('summary'),
            'content'       => $this->request->getString('content'),
            'tags'          => $this->request->getString('tags'),
            'allowcomments' => $this->request->getInt('allowcomments'),
            'type'          => $this->request->getString('type'),
            'blogID'        => $this->request->getInt('blogID'),
        ];

        $updateDB = $this->model->autosavePost($postID, $data);

        if($updateDB === false) {
            echo json_encode([
                'status' => 'failed',
                'message' => 'Could not run autosave - DB Update Error'
            ]);
        }
        elseif($updateDB > 0 && $updateDB !== $postID) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Post autosaved at ' . date('H:i'),
                'newpostid' => $updateDB
            ]);
        }
        else {
            echo json_encode([
                'status' => 'success',
                'message' => 'Post autosaved at ' . date('H:i')
            ]);
        }
    }

    /**
     * Get data for posts in JSON format
     * Handles GET /api/posts
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
    public function getList()
    {
        $blogID = $this->request->getInt('blogID', -1);
        $start  = $this->request->getInt('start', 1);
        $limit  = $this->request->getInt('limit', 10);
        $sort   = $this->request->getString('sort', 'name ASC');

        $showDrafts = $this->request->getString('showdrafts', 'false');
        $showDrafts = ($showDrafts == 'true') ? 1 : 0;

        $showScheduled = $this->request->getString('showscheduled', 'false');
        $showScheduled = ($showScheduled == 'true') ? 1 : 0;

        if(!$blog = $this->modelBlogs->getBlogById($blogID)) {
            die("{ 'error': 'Unable to find blog' }");
        }
        
        $result = [
            'blog'      => $blog,
            'postcount' => $this->model->countPostsOnBlog($blogID, $showDrafts, $showScheduled),
            'posts'     => $this->model->getPostsByBlog($blogID, $start, $limit, $showDrafts, $showScheduled, $sort),
        ];
        
        $this->response->setBody(JSONhelper::arrayToJSON($result));
    }

}
