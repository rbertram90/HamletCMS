<?php

namespace HamletCMS\FavouriteBlogs;

use HamletCMS\HamletCMS;

class FavouriteBlog {
    
    public $blog_id;
    public $user_id;

    public function blog() {
        /** @var \HamletCMS\Blog\model\Blogs $blogs_model */
        $blogs_model = HamletCMS::model('blogs');
        return $blogs_model->getBlogById($this->blog_id);
    }

}
