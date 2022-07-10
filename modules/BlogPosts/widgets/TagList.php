<?php

namespace HamletCMS\BlogPosts\widgets;

use HamletCMS\Widgets\AbstractWidget;
use HamletCMS\HamletCMS;

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
        /** @var \HamletCMS\BlogPosts\model\Posts */
        $model = HamletCMS::model('posts');

        $refererPath = parse_url($_SERVER['HTTP_REFERER'])['path'];
        parse_str(parse_url($_SERVER['HTTP_REFERER'])['query'], $refererQuery);

        if (($start = strpos($refererPath, '/tags/')) !== false) {
            $currentTags = substr($refererPath, $start + 6);
            $this->response->setVar('currentTagsList', explode(',', strtolower($currentTags)));
            $this->response->setVar('currentTags', $currentTags);
            $this->response->setVar('op', $refererQuery['op'] ?? 'or');
        }

        $this->response->setVar('tags', $model->countAllTagsByBlog($this->blog->id, $this->sort, $this->numtoshow, $this->lowerlimit));
        $this->response->write('widgets/tagsList.tpl', 'BlogPosts');
    }

    public function defaultSettings() {
        return [
            'heading' => 'Tags',
            'maxposts' => 10,
            'lowerlimit' => 1,
            'display' => 'list',
            'sort' => 'text',
        ];
    }

}