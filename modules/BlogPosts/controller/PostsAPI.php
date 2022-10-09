<?php

namespace HamletCMS\BlogPosts\controller;

use HamletCMS\GenericController;
use HamletCMS\HamletCMS;
use rbwebdesigns\core\JSONHelper;

/**
 * Class PostsAPI.
 * 
 * Access checks are mostly done in api_setup.php if blogID is passed as
 * a request parameter, and the permissions are defined in route.
 * 
 * @author R Bertram <ricky@rbwebdesigns.co.uk>
 */
class PostsAPI extends GenericController
{
    
    /** @var \HamletCMS\BlogPosts\model\Autosaves */
    protected $modelAutosaves;
    
    /** @var \HamletCMS\Blog\model\Blogs */
    protected $modelBlogs;

    /**
     * Posts controller constructor
     */
    public function __construct()
    {
        $this->modelAutosaves = HamletCMS::model('autosaves');
        $this->modelBlogs = HamletCMS::model('blogs');

        parent::__construct();
    }

    /**
     * Create a new post.
     */
    public function create()
    {
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
            $url = $this->model('posts')->createSafePostUrl($url);
        }
        if (!$url) {
            $newPost['link_override'] = 0;
            $url = $this->model('posts')->createSafePostUrl($newPost['title']);
        }
        $newPost['link'] = $url;

        // Validate unique URL
        if ($post = $this->model('posts')->getPostByURL($url, $blog->id)) {
            $this->response->setBody('{ "success": "false", "errorMessage": "Title is already in use" }');
            $this->response->code(400);
            return;
        }
        
        // Process custom fields for different post types
        HamletCMS::runHook('onBeforePostSaved', ['post' => &$newPost]);

        if (!$this->model('posts')->createPost($newPost)) {
            $this->response->setBody('{ "success": "false", "errorMessage": "Error creating post" }');
            $this->response->code(500);
            return;
        }

        // Get the post created - with ID and URL
        $post = $this->model('posts')->getPostByURL($url, $blog->id);

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

        if (!$post = $this->model('posts')->getPostById($postID)) {
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

        if (!$this->model('permissions')->userHasPermission('edit_all_posts', $blogID)) {
            $this->response->setBody('{ "success": false, "errorMessage": "Access denied" }');
            $this->response->code(403);
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
            $url = $this->model('posts')->createSafePostUrl($url);
        }
        if (!$url) {
            $updates['link_override'] = 0;
            $url = $this->model('posts')->createSafePostUrl($updates['title']);
        }

        $updates['link'] = $url;

        if ($this->model('posts')->count(['blog_id' => $blogID, 'link' => $url]) > 0) {

            $matchingPost = $this->model('posts')->getPostByURL($url, $blogID);
            if ($matchingPost->id != $postID) {
                $this->response->setBody('{ "success": "false", "errorMessage": "Title is already in use" }');
                $this->response->code(400);
                return;
            }
        }

        // Process custom fields for different post types
        HamletCMS::runHook('onBeforePostSaved', ['post' => &$updates]);

        $this->model('posts')->updatePost($post->id, $updates);
        $this->modelAutosaves->removeAutosave($post->id);
        
        // Re-fetch post data - will have updated URL alias
        $post = $this->model('posts')->getPostByURL($url, $blogID);

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

        if (!$post = $this->model('posts')->getPostById($postID)) {
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

        if ($newPostID = $this->model('posts')->clonePost($postID)) {
            $newPost = $this->model('posts')->getPostById($newPostID);
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
        $post = $this->model('posts')->getPostById($postID);

        if (!$post || !isset($post->blog_id) || $blogID != $post->blog_id) {
            $this->response->setBody('{ "success": false, "errorMessage": "Blog ID Mismatch" }');
            $this->response->code(400);
            return;
        }

        if ($this->model('posts')->delete(['id' => $post->id]) && $this->modelAutosaves->removeAutosave($post->id)) {
            HamletCMS::runHook('onPostDeleted', ['post' => $post]);
            $this->response->setBody('{ "success": true }');
        }
        else {
            $this->response->setBody('{ "success": false, "errorMessage": "Error deleting post" }');
            $this->response->code(500);
        }
    }
    
    /**
     * Handles /api/posts/bulkupdate
     * 
     * Update many posts, this method is responsible for checking permissions
     * as there are many different actions that can be taken, requiring different
     * access levels.
     */
    public function bulkUpdatePosts() {
        if ($this->request->method() !== 'POST') {
            $this->response->setBody('{ "success": false, "errorMessage": "Invalid request method" }');
            $this->response->code(400);
            return;
        };

        $blogID = $this->request->getInt('blogID');
        HamletCMS::$blogID = $blogID;
        $userID = HamletCMS::session()->currentUser['id'];
        $postIDs = $this->request->get('posts', []);

        // Check user is at least a contributor to the blog.
        if (!$this->model('contributors')->isBlogContributor($userID, $blogID)) {
            $this->response->setBody('{ "success": false, "errorMessage": "Access denied" }');
            $this->response->code(403);
            return;
        }
        if (count($postIDs) < 1) {
            $this->response->setBody('{ "success": false, "errorMessage": "Missing parameters" }');
            $this->response->code(400);
            return;
        }

        /** @var \HamletCMS\BlogPosts\Post[] */
        $posts = $this->model('posts')->get('*', ['id' => $postIDs]);
        
        // Check all posts belong to this blog
        foreach ($posts as $post) {
            if ($post->blog_id !== $blogID) {
                $this->response->setBody('{ "success": false, "errorMessage": "Parameter mismatch" }');
                $this->response->code(400);
                return;
            }
        }

        switch ($this->request->getString('action')) {
            case 'unpublish':
                if (!$this->model('permissions')->userHasPermission('publish_posts', $blogID)) {
                    $this->response->setBody('{ "success": false, "errorMessage": "Access denied" }');
                    $this->response->code(403);
                    return;
                }
                if ($this->model('posts')->update(['id' => $postIDs], ['draft' => 1])) {
                    $this->response->setBody('{ "success": true, "errorMessage": "Posts unpublished" }');
                    $this->response->code(200);
                    return;
                }
                break;
            case 'publish':
                if (!$this->model('permissions')->userHasPermission('publish_posts', $blogID)) {
                    $this->response->setBody('{ "success": false, "errorMessage": "Access denied" }');
                    $this->response->code(403);
                    return;
                }
                if ($this->model('posts')->update(['id' => $postIDs], ['draft' => 0])) {
                    $this->response->setBody('{ "success": true, "errorMessage": "Posts unpublished" }');
                    $this->response->code(200);
                    return;
                }
                break;
            case 'delete':
                if (!$this->model('permissions')->userHasPermission('delete_posts', $blogID)) {
                    $this->response->setBody('{ "success": false, "errorMessage": "Access denied" }');
                    $this->response->code(403);
                    return;
                }
                if ($this->model('posts')->delete(['id' => $postIDs])) {
                    $this->response->setBody('{ "success": true, "errorMessage": "Posts deleted" }');
                    $this->response->code(200);
                    return;
                }
                break;
            case 'clone':
                if (!$this->model('permissions')->userHasPermission('create_posts', $blogID)) {
                    $this->response->setBody('{ "success": false, "errorMessage": "Access denied" }');
                    $this->response->code(403);
                    return;
                }
                foreach ($postIDs as $post) {
                    if (!$this->model('posts')->clonePost($post)) {
                        $this->response->setBody('{ "success": true, "errorMessage": "Failed when cloning post" }');
                        $this->response->code(500);
                        return;
                    }
                }
                $this->response->setBody('{ "success": true, "errorMessage": "Posts cloned" }');
                $this->response->code(200);
                return;
        }

        $this->response->setBody('{ "success": false, "errorMessage": "No action taken" }');
        $this->response->code(400);        
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
            'title'     => $this->request->getString('title'),
            'summary'   => $this->request->getString('summary'),
            'content'   => $this->request->getString('content'),
            'tags'      => $this->request->getString('tags'),
            'type'      => $this->request->getString('type'),
            'blog_id'   => $this->request->getInt('blogID'),
            'timestamp' => $this->request->getString('date'),
        ];

        // Set the URL path
        $url = '';
        if ($this->request->get('overrideLink', false)) {
            $data['link_override'] = 1;
            $url = $this->request->getString('link');
            $url = $this->model('posts')->createSafePostUrl($url);
        }
        if (!$url) {
            $data['link_override'] = 0;
            $url = $this->model('posts')->createSafePostUrl($data['title']);
        }
        $data['link'] = $url;

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
            'postcount' => $this->model('posts')->countPostsOnBlog($blogID, $showDrafts, $showScheduled),
            'posts'     => $this->model('posts')->getPostsByBlog($blogID, $start, $limit, $showDrafts, $showScheduled, $sort),
        ];
        
        if (defined('CUSTOM_DOMAIN') && CUSTOM_DOMAIN) {
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
            $results = $this->model('posts')->search($blogID, $search);
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
