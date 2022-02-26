<?php

namespace HamletCMS\Widgets\widgets;

use HamletCMS\Widgets\AbstractWidget;

class TwitterFeed extends AbstractWidget
{
    public $heading;
    public $type;
    public $handle;
    public $list;
    public $limit;

    public function render() {
        $this->response->write('twitterFeed.tpl', 'Widgets');
    }

}