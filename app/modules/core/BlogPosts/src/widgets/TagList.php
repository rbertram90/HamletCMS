<?php

namespace rbwebdesigns\HamletCMS\BlogPosts\widgets;

use rbwebdesigns\HamletCMS\Widgets\AbstractWidget;
use rbwebdesigns\HamletCMS\HamletCMS;

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
        $model = HamletCMS::model('\rbwebdesigns\HamletCMS\BlogPosts\model\Posts');

        $this->response->setVar('tags', $model->countAllTagsByBlog($this->blog->id, $this->sort));
        $this->response->write('widgets/tagsList.tpl', 'BlogPosts');
    }

}