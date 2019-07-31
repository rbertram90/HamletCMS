<?php

namespace rbwebdesigns\blogcms\PostComments;

use rbwebdesigns\blogcms\BlogCMS;

class Comment {
    public $id;
    public $message;
    public $blog_id;
    public $post_id;
    public $user_id;
    public $approved;
    public $timestamp;

    protected $user = null;
    protected $post = null;

    /**
     * Get the comment author
     * 
     * @return \rbwebdesigns\blogcms\UserAccounts\User
     */
    public function author()
    {
        if (is_null($this->user)) {
            $usersModel = BlogCMS::model('\\rbwebdesigns\\blogcms\\UserAccounts\\model\\UserAccounts');
            $this->user = $usersModel->getById($this->user_id);            
        }
        return $this->user;
    }

    /**
     * Get the post the comment was made on
     * 
     * @return \rbwebdesigns\blogcms\BlogPosts\Post
     */
    public function post()
    {
        if (is_null($this->post)) {
            $postModel = BlogCMS::model('\\rbwebdesigns\\blogcms\\BlogPosts\\model\\Posts');
            $this->post = $postModel->getPostById($this->post_id);            
        }
        return $this->post;
    }

}