<?php

namespace HamletCMS\PostComments;

use HamletCMS\HamletCMS;

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
     * @return \HamletCMS\UserAccounts\User
     */
    public function author()
    {
        if (is_null($this->user)) {
            $usersModel = HamletCMS::model('\\HamletCMS\\UserAccounts\\model\\UserAccounts');
            $this->user = $usersModel->getById($this->user_id);            
        }
        return $this->user;
    }

    /**
     * Get the post the comment was made on
     * 
     * @return \HamletCMS\BlogPosts\Post
     */
    public function post()
    {
        if (is_null($this->post)) {
            $postModel = HamletCMS::model('\\HamletCMS\\BlogPosts\\model\\Posts');
            $this->post = $postModel->getPostById($this->post_id);            
        }
        return $this->post;
    }

}