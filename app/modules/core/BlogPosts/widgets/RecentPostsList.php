<?php

namespace HamletCMS\BlogPosts\widgets;

use HamletCMS\Widgets\AbstractWidget;
use HamletCMS\HamletCMS;

class RecentPostsList extends AbstractWidget
{

    public $heading;
    public $maxposts;
    public $style;

    public function render()
    {
        /** @var \HamletCMS\BlogPosts\model\Posts */
        $model = HamletCMS::model('\HamletCMS\BlogPosts\model\Posts');

        $this->response->setVar('blogUrl', $this->blog->url());
        $this->response->setVar('posts', $model->getPostsByBlog($this->blog->id, 1, $this->maxposts));
        $this->response->write('widgets/recentPosts.tpl', 'BlogPosts');
    }

}