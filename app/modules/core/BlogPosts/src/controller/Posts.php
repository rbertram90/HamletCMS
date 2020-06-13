<?php
namespace rbwebdesigns\HamletCMS\BlogPosts\controller;

use rbwebdesigns\HamletCMS\Contributors\model\ContributorGroups;
use rbwebdesigns\core\Sanitize;
use rbwebdesigns\core\AppSecurity;
use rbwebdesigns\HamletCMS\GenericController;
use rbwebdesigns\HamletCMS\HamletCMS;
use rbwebdesigns\HamletCMS\Menu;
use Codeliner\ArrayReader\ArrayReader;

/**
 * class Posts
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
class Posts extends GenericController
{
    /** @var \rbwebdesigns\HamletCMS\BlogPosts\model\Posts */
    protected $model;

    /** @var \rbwebdesigns\HamletCMS\Blog\model\Blogs */
    protected $modelBlogs;

    /** @var \rbwebdesigns\HamletCMS\Contributors\model\Contributors */
    protected $modelContributors;

    /** @var \rbwebdesigns\core\Request */
    protected $request;

    /** @var \rbwebdesigns\core\Response */
    protected $response;

    /** @var array Active blog */
    protected $blog = null;

    /** @var array Active post */
    protected $post = null;
    
    /**
     * Posts controller constructor
     */
    public function __construct()
    {
        $this->model = HamletCMS::model('\rbwebdesigns\HamletCMS\BlogPosts\model\Posts');
        $this->modelBlogs = HamletCMS::model('\rbwebdesigns\HamletCMS\Blog\model\Blogs');
        $this->modelContributors = HamletCMS::model('\rbwebdesigns\HamletCMS\Contributors\model\Contributors');
        $this->modelPermissions = HamletCMS::model('\rbwebdesigns\HamletCMS\Contributors\model\Permissions');

        $this->request = HamletCMS::request();
        $this->response = HamletCMS::response();

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
        $currentUser = HamletCMS::session()->currentUser;
        $action = $this->request->getUrlParameter(0);
        $access = true;

        if (!HamletCMS::$blogID) {
            $postID = $this->request->getUrlParameter(1);
            $this->post = $this->model->getPostById($postID);
            HamletCMS::$blogID = $this->post->blog_id;
        }

        $this->blog = HamletCMS::getActiveBlog();
        HamletCMS::$activeMenuLink = '/cms/posts/manage/'. $this->blog->id;

        // Check the user is a contributor of the blog to begin with
        if (!$this->modelContributors->isBlogContributor($currentUser['id'], $this->blog->id)) {
            $access = false;
        }

        // Check action specific permissions
        switch ($action) {
            case 'edit':
                if ($this->post->author_id !== $currentUser['id']) {
                    $access = $this->modelPermissions->userHasPermission('edit_all_posts', $this->blog->id);
                }
                elseif ($this->request->method() == 'POST' && $this->request->getInt('fld_draft') == 0) {
                    $access = $this->modelPermissions->userHasPermission('publish_posts', $this->blog->id);
                }
                break;

            case 'create':
                if ($this->request->method() == 'POST' && $this->request->getInt('fld_draft') == 0) {
                    $access = $this->modelPermissions->userHasPermission('publish_posts', $this->blog->id);
                }
                else {
                    $access = $this->modelPermissions->userHasPermission('create_posts', $this->blog->id);
                }
                break;

            case 'delete':
                $access = $this->modelPermissions->userHasPermission('delete_posts', $this->blog->id);
                break;
        }

        if (!$access) {
            $this->response->redirect('/cms', 'Access Denied', 'error');
        }
    }

    /**
     * Handles /posts/manage/<blogid>
     * Most of the heavy data is done in a seperate ajax call
     */
    public function manage()
    {
        $this->response->setVar('blog', $this->blog);
        $this->response->setTitle('Manage Posts - ' . $this->blog->name);
        $this->response->addScript('/js/showUserCard.js');
        $this->response->write('manage.tpl', 'BlogPosts');
    }
    
    /**
     * Handles POST /posts/create/<blogid>
     */
    public function create()
    {
        // if ($this->request->method() == 'POST') return $this->runCreatePost();

        $newPostMenu = new Menu('create_post');
        HamletCMS::runHook('onGenerateMenu', ['id' => 'create_post', 'menu' => &$newPostMenu]);

        $this->response->setVar('blog', $this->blog);
        $this->response->setTitle('New Post');
        $this->response->setVar('menu', $newPostMenu->getLinks());
        $this->response->write('newpostmenu.tpl', 'BlogPosts');
    }
    
    /**
     * Handles GET /posts/edit/<postID>
     */
    public function edit()
    {
        // Now passing this on individual modules
        HamletCMS::runHook('onViewEditPost', ['type' => $this->post->type]);
    }
    
    /**
     * Handles POST /posts/cancelsave/<postID>
     * 
     * Cancel action from post screen
     */
    public function cancelsave()
    {
        // Delete autosave
        $autosaveModel = HamletCMS::model('rbwebdesigns\HamletCMS\BlogPosts\model\Autosaves');
        $autosaveModel->removeAutosave($this->post->id);

        if ($this->post['initialautosave'] == 1) {
            // Delete post
            $this->model->delete(['id' => $this->post->id]);
        }

        $this->response->redirect('/cms/posts/manage/' . $this->blog->id);
    }
        
}
