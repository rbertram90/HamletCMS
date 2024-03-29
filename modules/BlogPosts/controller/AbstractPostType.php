<?php

namespace HamletCMS\BlogPosts\controller;

use HamletCMS\GenericController;
use HamletCMS\HamletCMS;

class AbstractPostType extends GenericController
{
    protected $blog;

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

        $this->response->setBreadcrumbs([
            $this->blog->name => $this->blog->url(),
            'Posts' => '/cms/posts/manage/' . $this->blog->id,
            'Create' => '/cms/posts/create/' . $this->blog->id,
            'Markdown' => null
        ]);
        $this->response->headerIcon = 'edit outline';
        $this->response->headerText = 'Create new post';

        $extraFields = [];
        HamletCMS::runHook('editPostForm', [
            'blog' => $this->blog,
            'post' => null,
            'fields' => &$extraFields
        ]);
        $this->response->setVar('customSettingFields', $extraFields);
    }

    /**
     * View edit post form
     */
    public function edit()
    {
        $postID = $this->request->getUrlParameter(1);
        $post = $this->model('posts')->getPostById($postID);

        if (!$post) {
            $this->response->redirect('/cms', 'Could not find post', 'error');
        }
        if ($autosave = $this->model('autosaves')->getAutosave($post->id)) {
            $post->autosave = $autosave;
        }

        $blog = $this->model('blogs')->getBlogById($post->blog_id);

        $this->response->setVar('post', $post);
        $this->response->setVar('blog', $blog);
        $this->response->setTitle('Editing - '. $post->title);

        $extraFields = [];
        HamletCMS::runHook('editPostForm', ['blog' => $blog, 'post' => &$post, 'fields' => &$extraFields]);
        $this->response->setVar('customSettingFields', $extraFields);
    }

}