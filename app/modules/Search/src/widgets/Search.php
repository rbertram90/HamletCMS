<?php

namespace rbwebdesigns\HamletCMS\Search\widgets;

use rbwebdesigns\HamletCMS\Widgets\AbstractWidget;
use rbwebdesigns\HamletCMS\HamletCMS;

class Search extends AbstractWidget
{

    public function render()
    {
        $this->response->setVar('blog_url', $this->blog->url());
        $this->response->write('viewSearchWidget.tpl', 'Search');
    }

}