<?php

namespace HamletCMS\BlogPosts\widgets;

use HamletCMS\Widgets\AbstractWidget;
use HamletCMS\HamletCMS;

class TrendingPosts extends AbstractWidget
{

    public function render()
    {
        $model = HamletCMS::model('\HamletCMS\BlogPosts\model\Posts');

        $this->response->setVar('blogUrl', $this->blog->url());
        $this->response->setVar('posts', $model->getTrendingPosts($this->blog->id));
        $this->response->write('widgets/recentPosts.tpl', 'BlogPosts');
    }

}