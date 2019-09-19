<?php

namespace rbwebdesigns\HamletCMS\BlogPosts\widgets;

use rbwebdesigns\HamletCMS\Widgets\AbstractWidget;
use rbwebdesigns\HamletCMS\HamletCMS;

class RecentPostsList extends AbstractWidget
{

    public function render()
    {
        $model = HamletCMS::model('\rbwebdesigns\HamletCMS\BlogPosts\model\Posts');

        $this->response->setVar('blogUrl', $this->blog->url());
        $this->response->setVar('posts', $model->getPostsByBlog($this->blog->id));
        $this->response->write('widgets/recentPosts.tpl', 'BlogPosts');
    }

}