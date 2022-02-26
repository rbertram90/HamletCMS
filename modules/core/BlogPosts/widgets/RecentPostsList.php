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
        $model = HamletCMS::model('posts');

        $this->response->setVar('blogUrl', $this->blog->url());
        $this->response->setVar('posts', $model->getVisiblePosts($this->blog->id, $this->maxposts));
        $this->response->write('widgets/recentPosts.tpl', 'BlogPosts');
    }

    public function defaultSettings() {
        return [
            'heading' => 'Recent Posts',
            'maxposts' => 6,
            'style' => 'none',
        ];
    }

}