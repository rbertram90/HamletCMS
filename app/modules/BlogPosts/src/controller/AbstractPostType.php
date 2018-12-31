<?php

namespace rbwebdesigns\blogcms\BlogPosts\controller;

use rbwebdesigns\blogcms\GenericController;
use rbwebdesigns\blogcms\BlogCMS;

class AbstractPostType extends GenericController
{
    protected $blog;
    protected $model;
    protected $modelBlogs;

    public function __construct() {
        $this->model = BlogCMS::model('\rbwebdesigns\blogcms\BlogPosts\model\Posts');
        $this->modelBlogs = BlogCMS::model('\rbwebdesigns\blogcms\Blog\model\Blogs');
        $this->modelPermissions = BlogCMS::model('\rbwebdesigns\blogcms\Contributors\model\Permissions');

        parent::__construct();
    }

    /**
     * View the create post form or save submitted post
     * 
     * If saving will exit here and redirect.
     */
    public function create()
    {
        $this->blog = BlogCMS::getActiveBlog();
        $this->response->setVar('blog', $this->blog);
        $this->response->setTitle('Create blog post');

        $extraFields = [];
        BlogCMS::runHook('editPostForm', ['blog' => $blog, 'post' => &$post, 'fields' => &$extraFields]);
        $this->response->setVar('customSettingFields', $extraFields);
    }

    /**
     * View edit post form
     */
    public function edit()
    {
        $postID = $this->request->getUrlParameter(1);
        $post = $this->model->getPostById($postID);

        if (!$post) {
            $this->response->redirect('/', 'Could not find post', 'error');
        }

        if (!$this->modelPermissions->userHasPermission('edit_all_posts', $post->blog_id)) {
            $this->response->redirect('/', 'You do not have permission to edit this post', 'error');
        }

        $blog = $this->modelBlogs->getBlogById($post->blog_id);

        $this->response->setVar('post', $post);
        $this->response->setVar('blog', $blog);
        $this->response->setTitle('Editing - '. $post->title);

        $extraFields = [];
        BlogCMS::runHook('editPostForm', ['blog' => $blog, 'post' => &$post, 'fields' => &$extraFields]);
        $this->response->setVar('customSettingFields', $extraFields);
    }

}