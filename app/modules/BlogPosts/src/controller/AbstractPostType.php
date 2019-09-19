<?php

namespace rbwebdesigns\HamletCMS\BlogPosts\controller;

use rbwebdesigns\HamletCMS\GenericController;
use rbwebdesigns\HamletCMS\HamletCMS;

class AbstractPostType extends GenericController
{
    protected $blog;
    protected $model;
    protected $modelBlogs;

    public function __construct() {
        $this->model = HamletCMS::model('\rbwebdesigns\HamletCMS\BlogPosts\model\Posts');
        $this->modelBlogs = HamletCMS::model('\rbwebdesigns\HamletCMS\Blog\model\Blogs');
        $this->modelPermissions = HamletCMS::model('\rbwebdesigns\HamletCMS\Contributors\model\Permissions');

        parent::__construct();
    }

    /**
     * View the create post form or save submitted post
     * 
     * If saving will exit here and redirect.
     */
    public function create()
    {
        $this->blog = HamletCMS::getActiveBlog();
        $this->response->setVar('blog', $this->blog);
        $this->response->setTitle('Create blog post');

        $extraFields = [];
        HamletCMS::runHook('editPostForm', ['blog' => $blog, 'post' => [], 'fields' => &$extraFields]);
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
            $this->response->redirect('/cms', 'Could not find post', 'error');
        }

        $blog = $this->modelBlogs->getBlogById($post->blog_id);

        $this->response->setVar('post', $post);
        $this->response->setVar('blog', $blog);
        $this->response->setTitle('Editing - '. $post->title);

        $extraFields = [];
        HamletCMS::runHook('editPostForm', ['blog' => $blog, 'post' => &$post, 'fields' => &$extraFields]);
        $this->response->setVar('customSettingFields', $extraFields);
    }

}