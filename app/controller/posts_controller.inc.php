<?php
namespace rbwebdesigns\blogcms;

use rbwebdesigns\blogcms\model\ContributorGroups;
use rbwebdesigns\core\Sanitize;
use rbwebdesigns\core\AppSecurity;
use Codeliner\ArrayReader\ArrayReader;

/**
 * class PostsController
 * 
 * This is the controller which acts as the intermediatory between the
 * model (database) and the view. Any requests to the model are sent from
 * here rather than the view.
 * 
 * Routes:
 *  /posts/manage/<blogid>
 *  /posts/create/<blogid>
 *  /posts/create/<blogid>/standard
 *  /posts/edit/<postid>
 *  /posts/delete/<postid>
 * 
 * @author R Bertram <ricky@rbwebdesigns.co.uk>
 */
class PostsController extends GenericController
{
    /**
     * @var \rbwebdesigns\blogcms\model\Posts
     */
    protected $model;
    /**
     * @var \rbwebdesigns\blogcms\model\Blogs
     */
    protected $modelBlogs;
    /**
     * @var \rbwebdesigns\blogcms\model\Contributors
     */
    protected $modelContributors;
    /**
     * @var \rbwebdesigns\core\Request
     */
    protected $request;
    /**
     * @var \rbwebdesigns\core\Response
     */
    protected $response;
    /**
     * @var array Active blog
     */
    protected $blog = null;
    /**
     * @var array Active post
     */
    protected $post = null;
    

    public function __construct()
    {
        $this->model = BlogCMS::model('\rbwebdesigns\blogcms\model\Posts');
        $this->modelBlogs = BlogCMS::model('\rbwebdesigns\blogcms\model\Blogs');
        $this->modelContributors = BlogCMS::model('\rbwebdesigns\blogcms\model\Contributors');

        BlogCMS::$activeMenuLink = 'posts';

        $this->request = BlogCMS::request();
        $this->response = BlogCMS::response();

        $this->setup();
    }
    
    /**
     * Setup controller
     * 
     * 1. Gets the key records that will be used for any request to keep
     *    the code DRY (Blog and Post)
     * 
     * 2. Checks the user has permissions to run the request
     */
    protected function setup()
    {
        $currentUser = BlogCMS::session()->currentUser;
        $action = $this->request->getUrlParameter(0);
        $access = true;

        if (!BlogCMS::$blogID) {
            $postID = $this->request->getUrlParameter(1);
            $this->post = $this->model->getPostById($postID);

            BlogCMS::$blogID = $this->post['blog_id'];
        }

        $this->blog = BlogCMS::getActiveBlog();

        // Check the user is a contributor of the blog to begin with
        if (!$this->modelContributors->isBlogContributor($this->blog['id'], $currentUser['id'])) {
            $access = false;
        }

        // Check action specific permissions
        switch ($action) {
            case 'edit':
                if ($this->post['author_id'] != $currentUser['id']) {
                    $access = $this->modelContributors->userHasPermission($currentUser['id'], $this->blog['id'], ContributorGroups::GROUP_EDIT_POSTS);
                }
                elseif ($this->request->method() == 'POST' && $this->request->getInt('fld_draft') == 0) {
                    $access = $this->modelContributors->userHasPermission($currentUser['id'], $this->blog['id'], ContributorGroups::GROUP_PUBLISH_POSTS);
                }
                break;

            case 'create':
                if ($this->request->method() == 'POST' && $this->request->getInt('fld_draft') == 0) {
                    $access = $this->modelContributors->userHasPermission($currentUser['id'], $this->blog['id'], ContributorGroups::GROUP_PUBLISH_POSTS);
                }
                else {
                    $access = $this->modelContributors->userHasPermission($currentUser['id'], $this->blog['id'], ContributorGroups::GROUP_CREATE_POSTS);
                }
                break;

            case 'delete':
                $access = $this->modelContributors->userHasPermission($currentUser['id'], $this->blog['id'], ContributorGroups::GROUP_DELETE_POSTS);
                break;
        }

        if (!$access) {
            $this->response->redirect('/cms', '403 Access Denied', 'error');
        }
    }

    /**
     * Handles /posts/manage/<blogid>
     * Most of the heavy data is done in a seperate ajax call
     */
    public function manage()
    {
        $this->response->setVar('blog', $this->blog);
        $this->response->setTitle('Manage Posts - ' . $this->blog['name']);
        $this->response->addScript('/js/showUserCard.js');
        $this->response->write('posts/manage.tpl');
    }
    
    /**
     * Handles POST /posts/create/<blogid>
     */
    public function create()
    {
        if ($this->request->method() == 'POST') return $this->runCreatePost();

        $this->response->setVar('blog', $this->blog);
        $this->response->setTitle('New Post');
        
        $this->response->addScript('/resources/js/rbwindow.css');
        $this->response->addScript('/resources/js/rbrtf.css');
        $this->response->addStylesheet('/resources/css/rbwindow.css');
        $this->response->addStylesheet('/resources/css/rbrtf.css');
        
        switch ($this->request->getUrlParameter(2)) {
            case 'video':
            $this->response->write('posts/videopost.tpl');
            break;
            
            case 'gallery':
            $this->response->write('posts/gallerypost.tpl');
            break;
            
            case 'standard':
            $this->response->write('posts/standardpost.tpl');
            break;
            
            default:
            $this->response->write('posts/newpostmenu.tpl');
            break;
        }
    }
    
    /**
     * Handles GET /posts/edit/<postID>
     */
    public function edit()
    {
        if ($this->request->method() == 'POST') return $this->runEditPost();
        
        if ($this->post['type'] == 'gallery') {
            $this->post['gallery_imagelist'] = explode(',', $this->post['gallery_imagelist']);
        }

        if ($this->post['initialautosave'] == 1) {
            // get the most recent content from the actual autosave record
            $autosave = $this->model->getAutosave($this->post['id']);

            if (getType($autosave) == 'array') {
                $this->post = array_merge($this->post, $autosave);
            }

            $this->model->update(['id' => $this->post['id']], ['initialautosave' => 0]);
        }
        elseif ($this->model->autosaveExists($this->post['id'])) {
            // Has been edited without being saved
            $this->post['autosave'] = $this->model->getAutosave($this->post['id']);
        }
                
        $this->response->setVar('post', $this->post);
        $this->response->setVar('blog', $this->blog);
        $this->response->setTitle('Edit Post - ' . $this->post['title']);
        
        $this->response->addScript('/resources/js/rbwindow.js');
        $this->response->addScript('/resources/js/rbrtf.js');
        $this->response->addStylesheet('/resources/css/rbwindow.css');
        $this->response->addStylesheet('/resources/css/rbrtf.css');

        switch ($this->post['type']) {
            case 'video':
            $this->response->write('posts/videopost.tpl');
            break;
            
            case 'gallery':
            $this->response->write('posts/gallerypost.tpl');
            break;
            
            default:
            $this->response->write('posts/standardpost.tpl');
            break;
        }
    }
    
    /**
     * Handles POST /posts/create/<postID>
     */
    protected function runCreatePost()
    {
        $posttime = strtotime($this->request->getString('fld_postdate'));
        
        if (checkdate(date("m", $posttime), date("d", $posttime), date("Y", $posttime))) {
            $postdate = date("Y-m-d H:i:00", $posttime);
        }
        else {
            $postdate = date("Y-m-d H:i:00");
        }
        
        $newPost = [
            'title'           => $this->request->getString('fld_posttitle'),
            'content'         => $this->request->getString('fld_postcontent'),
            'tags'            => $this->request->getString('fld_tags'),
            'blog_id'         => $this->blog['id'],
            'draft'           => $this->request->getInt('fld_draft'),
            'allowcomments'   => $this->request->getInt('fld_allowcomment'),
            'type'            => $this->request->getString('fld_posttype'),
            'initialautosave' => 0,
            'timestamp'       => $postdate
        ];

        if (strlen($newPost['title']) == 0) {
            $this->response->redirect('/cms/posts/manage/' . $this->blog['id'], 'Please provide a title', 'error');
        }
        
        if ($newPost['type'] == 'video') {
            $newPost['videoid'] = $this->request->getString('fld_postvideoID');
            $newPost['videosource'] = $this->request->getString('fld_postvideosource');
        }
        
        if ($newPost['type'] == 'gallery') {
            $newPost['gallery_imagelist'] = $this->request->get('fld_gallery_imagelist');
        }

        if($postID = $this->request->getInt('fld_postid', false)) {
            // This should be the case as it should have been created when the autosave run
            if ($this->model->updatePost($postID, $newPost)) {
                $this->model->removeAutosave($postID);
            }
            else {
                $this->response->redirect('/cms/posts/edit/' . $postID, 'Error updating post', 'error');
            }
        }
        elseif (!$this->model->createPost($newPost)) {
            $this->response->redirect('/cms/posts/manage/' . $this->blog['id'], 'Error creating post', 'error');
        }

        $this->response->redirect('/cms/posts/manage/' . $this->blog['id'], 'Post created', 'success');
    }
    
    /**
     * Handles POST /posts/cancelsave/<postID>
     * 
     * Cancel action from post screen
     */
    public function cancelsave()
    {
        // Delete autosave
        $this->model->removeAutosave($this->post['id']);

        if ($this->post['initialautosave'] == 1) {
            // Delete post
            $this->model->delete(['id' => $this->post['id']]);
        }

        $this->response->redirect('/cms/posts/manage/' . $this->blog['id']);
    }
    
    /**
     * Handles POST /posts/edit/<postID>
     * 
     * Edit an existing blog post
     */
    public function runEditPost()
    {
        // Check & Format date
        $posttime = strtotime($this->request->getString('fld_postdate'));
        
        if (checkdate(date("m", $posttime), date("d", $posttime), date("Y", $posttime))) {
            $postdate = date("Y-m-d H:i:00", $posttime);
        }
        else {
            $postdate = $arraypost['timestamp']; // Keep to original
        }
        
        $updates = [
            'title'           => $this->request->getString('fld_posttitle'),
            'content'         => $this->request->getString('fld_postcontent'),
            'tags'            => $this->request->getString('fld_tags'),
            'draft'           => $this->request->getInt('fld_draft'),
            'allowcomments'   => $this->request->getInt('fld_allowcomment'),
            'initialautosave' => 0,
            'timestamp'       => $postdate
        ];

        if (strlen($updates['title']) == 0) {
            $this->response->redirect('/cms/posts/manage/' . $this->blog['id'], 'Please provide a title', 'error');
        }
        
        if ($this->post['type'] == 'video') {
            $updates['videoid']     = $this->request->getString('fld_postvideoID');
            $updates['videosource'] = $this->request->getString('fld_postvideosource');
        }
        if ($this->post['type'] == 'gallery') {
            $updates['gallery_imagelist'] = $this->request->get('fld_gallery_imagelist');
        }
        
        $this->model->updatePost($this->post['id'], $updates);
        $this->model->removeAutosave($this->post['id']);
        
        $this->response->redirect('/cms/posts/edit/' . $this->post['id'], 'Save successful', 'success');
    }
    
    /**
     * Handles /posts/delete/<postID>
     * 
     * @todo make sure there are no pages with this post ID
     */
    public function delete()
    {
        if($delete = $this->model->delete(['id' => $this->post['id']]) && $this->model->removeAutosave($this->post['id'])) {
            $this->response->redirect('/cms/posts/manage/' . $this->post['blog_id'], 'Blog post deleted', 'success');
        }
        else {
            $this->response->redirect('/cms/posts/manage/' . $this->post['blog_id'], 'Blog post deleted', 'error');
        }
    }
    
}
