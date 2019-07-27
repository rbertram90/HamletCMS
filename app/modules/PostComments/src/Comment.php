<?php

namespace rbwebdesigns\blogcms\PostComments;

class Comment {
    public $id;
    public $message;
    public $blog_id;
    public $post_id;
    public $user_id;
    public $approved;
    public $timestamp;
}