<?php
namespace HamletCMS\BlogPosts\controller;

use HamletCMS\GenericController;
use HamletCMS\HamletCMS;
use HamletCMS\Menu;

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
    /** @var \HamletCMS\Blog\Blog Active blog */
    protected $blog = null;

    /** @var \HamletCMS\BlogPosts\Post Active post */
    protected $post = null;
    
    /**
     * Posts controller constructor
     */
    public function __construct()
    {
        parent::__construct();
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
            $this->post = $this->model('posts')->getPostById($postID);
            HamletCMS::$blogID = $this->post->blog_id;
        }

        $this->blog = HamletCMS::getActiveBlog();
        HamletCMS::$activeMenuLink = '/cms/posts/manage/'. $this->blog->id;

        // Check the user is a contributor of the blog to begin with
        if (!$this->model('contributors')->isBlogContributor($currentUser['id'], $this->blog->id)) {
            $access = false;
        }

        // Check action specific permissions
        switch ($action) {
            case 'edit':
                if ($this->post->author_id !== $currentUser['id']) {
                    $access = $this->model('permissions')->userHasPermission('edit_all_posts', $this->blog->id);
                }
                elseif ($this->request->method() == 'POST' && $this->request->getInt('fld_draft') == 0) {
                    $access = $this->model('permissions')->userHasPermission('publish_posts', $this->blog->id);
                }
                break;

            case 'create':
                if ($this->request->method() == 'POST' && $this->request->getInt('fld_draft') == 0) {
                    $access = $this->model('permissions')->userHasPermission('publish_posts', $this->blog->id);
                }
                else {
                    $access = $this->model('permissions')->userHasPermission('create_posts', $this->blog->id);
                }
                break;

            case 'delete':
                $access = $this->model('permissions')->userHasPermission('delete_posts', $this->blog->id);
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
        $this->response->setTitle('Manage posts - ' . $this->blog->name);
        $this->response->setBreadcrumbs([
            $this->blog->name => $this->blog->url(),
            'Posts' => null
        ]);
        $this->response->headerIcon = 'copy outline';
        $this->response->headerText = $this->blog->name . ': Manage posts';
        $this->response->addScript('/hamlet/js/showUserCard.js');
        $this->response->addScript('/hamlet/js/managePosts.js');
        $this->response->write('manage.tpl', 'BlogPosts');
    }
    
    /**
     * Handles POST /posts/create/<blogid>
     */
    public function create()
    {
        $newPostMenu = new Menu('create_post');
        HamletCMS::runHook('onGenerateMenu', ['id' => 'create_post', 'menu' => &$newPostMenu]);

        $this->response->setBreadcrumbs([
            $this->blog->name => $this->blog->url(),
            'Posts' => '/cms/posts/manage' .  $this->blog->id,
            'Create' => null
        ]);
        $this->response->headerIcon = 'file alternate outline';
        $this->response->headerText = $this->blog->name . ': Create new post';

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
        $this->response->setBreadcrumbs([
            $this->blog->name => $this->blog->url(),
            $this->post->title => $this->post->url(),
            'Edit' => null
        ]);
        $this->response->headerIcon = 'edit outline';
        $this->response->headerText = 'Editing post: &ldquo;' . $this->post->title . '&rdquo;';

        // Now passing this on individual modules
        HamletCMS::runHook('onViewEditPost', ['type' => $this->post->type]);
    }
    
    /**
     * Handles GET /posts/delete/<postID>
     */
    public function delete()
    {
        if ($this->model('posts')->delete(['id' => $this->post->id]) && $this->model('autosaves')->removeAutosave($this->post->id)) {
            HamletCMS::runHook('onPostDeleted', ['post' => $this->post]);
            $this->response->redirect('/cms/posts/manage/' . $this->blog->id, 'Post deleted', 'success');
        }
        else {
            $this->response->redirect('/cms/posts/manage/' . $this->blog->id, 'Unable to delete post', 'error');
        }
    }

    /**
     * Handles POST /posts/cancelsave/<postID>
     * 
     * Cancel action from post screen
     */
    public function cancelsave()
    {
        // Delete autosave
        $this->model('autosaves')->removeAutosave($this->post->id);

        if ($this->post->initialautosave == 1) {
            // Delete post
            $this->model('posts')->delete(['id' => $this->post->id]);
        }

        $this->response->redirect('/cms/posts/manage/' . $this->blog->id);
    }
    
}
