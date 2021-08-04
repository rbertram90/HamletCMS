<?php

namespace HamletCMS\FavouriteBlogs;

use HamletCMS\HamletCMS;

class FavouritePost {

    public $post_id;
    public $user_id;

    public function post() {
        /** @var \HamletCMS\BlogPosts\model\Posts $posts_model */
        $posts_model = HamletCMS::model('\\HamletCMS\\BlogPosts\\model\\Posts');
        return $posts_model->getPostById($this->post_id);
    }

}
