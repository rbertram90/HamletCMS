<?php

namespace rbwebdesigns\blogcms\BlogPosts\widgets;

use rbwebdesigns\blogcms\Widgets\AbstractWidget;
use rbwebdesigns\blogcms\BlogCMS;

class TagList extends AbstractWidget
{

    // Widget settings
    public $heading;
    public $numtoshow;
    public $lowerlimit;
    public $display;
    public $sort;

    public function render()
    {
        $model = BlogCMS::model('\rbwebdesigns\blogcms\BlogPosts\model\Posts');

        $this->response->setVar('tags', $model->countAllTagsByBlog($this->blog->id, $this->sort));
        $this->response->write('widgets/tagsList.tpl', 'BlogPosts');
    }

}