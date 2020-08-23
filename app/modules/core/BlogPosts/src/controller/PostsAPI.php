<?php
namespace rbwebdesigns\HamletCMS\BlogPosts\controller;

use rbwebdesigns\HamletCMS\GenericController;
use rbwebdesigns\HamletCMS\HamletCMS;
use rbwebdesigns\core\JSONHelper;

/**
 * Class PostsAPI
 * 
 * @author R Bertram <ricky@rbwebdesigns.co.uk>
 */
class PostsAPI extends GenericController
{
    /** @var \rbwebdesigns\HamletCMS\BlogPosts\model\Posts */
    protected $model;

    /** @var \rbwebdesigns\HamletCMS\BlogPosts\model\Autosaves */
    protected $modelAutosaves;
    
    /** @var \rbwebdesigns\HamletCMS\Blog\model\Blogs */
    protected $modelBlogs;

    /**
     * Posts controller constructor
     */
    public function __construct()
    {
        $this->model = HamletCMS::model('\rbwebdesigns\HamletCMS\BlogPosts\model\Posts');
        $this->modelAutosaves = HamletCMS::model('\rbwebdesigns\HamletCMS\BlogPosts\model\Autosaves');
        $this->modelBlogs = HamletCMS::model('\rbwebdesigns\HamletCMS\Blog\model\Blogs');

        parent::__construct();

        /*
        if (!HamletCMS::$blogID) {
            $postID = $this->request->getUrlParameter(1);
            $this->post = $this->model->getPostById($postID);
            HamletCMS::$blogID = $this->post['blog_id'];
        }

        $this->blog = HamletCMS::getActiveBlog();
        */
    }

    /**
     * Create a new post
     * 
     * This is run as ajax request
     */
    public function create()
    {
        // We've already validated that user has got access to create this post
        $blogID = $this->request->getInt('blogID', false);
        $blog = $this->modelBlogs->getBlogById($blogID);
        
        // Ensure we've got a valid date
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
            'blog_id'         => $blog->id,
            'draft'           => $this->request->getInt('draft'),
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

        // Set the URL path
        $url = '';
        if ($this->request->get('overrideLink', false)) {
            $newPost['link_override'] = 1;
            $url = $this->request->getString('link');
            $url = $this->model->createSafePostUrl($url);
        }
        if (!$url) {
            $newPost['link_override'] = 0;
            $url = $this->model->createSafePostUrl($newPost['title']);
        }
        $newPost['link'] = $url;

        // Validate unique URL
        if ($post = $this->model->getPostByURL($url, $blog->id)) {
            $this->response->setBody('{ "success": "false", "errorMessage": "Title is already in use" }');
            $this->response->code(400);
            return;
        }
        
        // Process custom fields for different post types
        HamletCMS::runHook('onBeforePostSaved', ['post' => &$newPost]);

        if (!$this->model->createPost($newPost)) {
            $this->response->setBody('{ "success": "false", "errorMessage": "Error creating post" }');
            $this->response->code(500);
            return;
        }

        // Get the post created - with ID and URL
        $post = $this->model->getPostByURL($url, $blog->id);

        HamletCMS::runHook('onPostCreated', ['post' => $post]);

        $this->response->setBody('{ "success": "true", "post": '. json_encode($post) .' }');
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
        if ($post->blog_id != $blogID) {
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
            $postdate = $post->timestamp; // Keep to original
        }
        
        $updates = [
            'id'              => $postID,
            'type'            => $post->type,
            'title'           => $this->request->getString('title'),
            'summary'         => $this->request->getString('summary'),
            'content'         => $this->request->get('content'),
            'tags'            => $this->request->getString('tags'),
            'teaser_image'    => $this->request->getString('teaserImage'),
            'draft'           => $this->request->getInt('draft'),
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
        $url = '';
        if (filter_var($this->request->get('overrideLink'), FILTER_VALIDATE_BOOLEAN)) {
            $updates['link_override'] = 1;
            $url = $this->request->getString('link');
            $url = $this->model->createSafePostUrl($url);
        }
        if (!$url) {
            $updates['link_override'] = 0;
            $url = $this->model->createSafePostUrl($updates['title']);
        }

        $updates['link'] = $url;

        if ($this->model->count(['blog_id' => $blogID, 'link' => $url]) > 0) {

            $matchingPost = $this->model->getPostByURL($url, $blogID);
            if ($matchingPost->id != $postID) {
                $this->response->setBody('{ "success": "false", "errorMessage": "Title is already in use" }');
                $this->response->code(400);
                return;
            }
        }

        // Process custom fields for different post types
        HamletCMS::runHook('onBeforePostSaved', ['post' => &$updates]);

        $this->model->updatePost($post->id, $updates);
        $this->modelAutosaves->removeAutosave($post->id);
        
        // Re-fetch post data - will have updated URL alias
        $post = $this->model->getPostByURL($url, $blogID);

        HamletCMS::runHook('onPostUpdated', ['post' => $post]);

        $this->response->setBody('{ "success": true, "post": '. json_encode($post) .' }');
    }
    
    /**
     * Clone a post
     */
    public function clonePost()
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
        if ($post->blog_id != $blogID) {
            $this->response->setBody('{ "success": false, "errorMessage": "Blog ID mismatch" }');
            $this->response->code(406);
            return;
        }

        if ($newPostID = $this->model->clonePost($postID)) {
            $newPost = $this->model->getPostById($newPostID);
            HamletCMS::runHook('onPostCreated', ['post' => $newPost]);
            $this->response->setBody('{ "success": true, "newPostID": '.$newPostID.' }');
        }
        else {
            $this->response->setBody('{ "success": false, "errorMessage": "Error cloning post" }');
            $this->response->code(500);
        }
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

        if (!$post || !isset($post->blog_id) || $blogID != $post->blog_id) {
            $this->response->setBody('{ "success": false, "errorMessage": "Blog ID Mismatch" }');
            $this->response->code(400);
            return;
        }

        if ($this->model->delete(['id' => $post->id]) && $this->modelAutosaves->removeAutosave($post->id)) {
            HamletCMS::runHook('onPostDeleted', ['post' => $post]);
            $this->response->setBody('{ "success": true }');
        }
        else {
            $this->response->setBody('{ "success": false, "errorMessage": "Error deleting post" }');
            $this->response->code(500);
        }
    }
    
    /**
     * Handles /api/posts/autosave/<postID>
     * 
     * @todo Has access been checked?
     */
    public function autosave()
    {
        $postID = $this->request->getInt('postID');

        $data = [
            'title'         => $this->request->getString('title'),
            'summary'       => $this->request->getString('summary'),
            'content'       => $this->request->getString('content'),
            'tags'          => $this->request->getString('tags'),
            'type'          => $this->request->getString('type'),
            'blogID'        => $this->request->getInt('blogID'),
            'timestamp'     => $this->request->getInt('date'),
        ];

        // Populate custom fields
        HamletCMS::runHook('onBeforeAutosave', ['post' => &$data]);

        $updateDB = $this->modelAutosaves->autosavePost($postID, $data);

        if ($updateDB === false) {
            echo json_encode([
                'status' => 'failed',
                'message' => 'Could not run autosave - DB Update Error'
            ]);
        }
        elseif ($updateDB > 0 && $updateDB !== $postID) {
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
     *  showdrafts (optional) - include draft posts (true / false)
     *  showscheduled (optional) - include scheduled posts (true / false)
     */
    public function getList()
    {
        $blogID = $this->request->getInt('blogID', -1);
        $start  = $this->request->getInt('start', 1);
        $limit  = $this->request->getInt('limit', 10);
        $sort   = $this->request->getString('sort', 'name ASC');

        $showDrafts = $this->request->getString('showdrafts', false);
        $showDrafts = ($showDrafts == 'true') ? 1 : 0;

        $showScheduled = $this->request->getString('showscheduled', false);
        $showScheduled = ($showScheduled == 'true') ? 1 : 0;

        if (!$blog = $this->modelBlogs->getBlogById($blogID)) {
            die("{ 'error': 'Unable to find blog' }");
        }
        
        $result = [
            'blog'      => $blog,
            'postcount' => $this->model->countPostsOnBlog($blogID, $showDrafts, $showScheduled),
            'posts'     => $this->model->getPostsByBlog($blogID, $start, $limit, $showDrafts, $showScheduled, $sort),
        ];
        
        if (CUSTOM_DOMAIN) {
            $this->response->addHeader('Access-Control-Allow-Origin', $blog->domain);
        }

        $this->response->setBody(JSONhelper::arrayToJSON($result));
    }

    /**
     * Results are keyed for a semantic ui dropdown
     */
    public function lookupTitle()
    {
        $blogID = $this->request->getInt('blogID', false);
        $search = $this->request->getString('q', false);

        if (!$blogID || !$search || strlen($search) == 0) {
            $results = [];
        }
        else {
            $results = $this->model->search($blogID, $search);
        }

        $data = [];

        foreach ($results as $result) {
            $data[] = [
                'name' => $result->title,
                'value' => $result->id,
            ];
        }

        $this->response->setBody(JSONhelper::arrayToJSON(['success' => true,
            'results' => $data
        ]));
    }

}
