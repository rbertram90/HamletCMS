<?php

namespace rbwebdesigns\blogcms\BlogPosts\widgets;

use rbwebdesigns\blogcms\Widgets\AbstractWidget;
use rbwebdesigns\blogcms\BlogCMS;

class RecentPostsList extends AbstractWidget
{

    public function render()
    {
        $model = BlogCMS::model('\rbwebdesigns\blogcms\BlogPosts\model\Posts');

        $this->response->setVar('blogUrl', $this->blog->url());
        $this->response->setVar('posts', $model->getPostsByBlog($this->blog->id));
        $this->response->write('recentPosts.tpl', 'BlogPosts');
    }

}