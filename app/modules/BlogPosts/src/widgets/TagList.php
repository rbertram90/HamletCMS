<?php

namespace rbwebdesigns\blogcms\BlogPosts\widgets;

use rbwebdesigns\blogcms\Widgets\AbstractWidget;
use rbwebdesigns\blogcms\BlogCMS;

class TagList extends AbstractWidget
{

    public function render()
    {
        $model = BlogCMS::model('\rbwebdesigns\blogcms\BlogPosts\model\Posts');

        $this->response->setVar('tags', $model->countAllTagsByBlog($this->blog->id));
        $this->response->write('tagsList.tpl', 'BlogPosts');
    }

}