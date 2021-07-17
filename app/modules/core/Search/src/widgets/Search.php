<?php

namespace HamletCMS\Search\widgets;

use HamletCMS\Widgets\AbstractWidget;
use HamletCMS\HamletCMS;

class Search extends AbstractWidget
{

    public function render()
    {
        $this->response->setVar('blog_url', $this->blog->url());
        $this->response->write('viewSearchWidget.tpl', 'Search');
    }

}