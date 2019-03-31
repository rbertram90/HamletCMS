<?php

namespace rbwebdesigns\blogcms\Search\widgets;

use rbwebdesigns\blogcms\Widgets\AbstractWidget;
use rbwebdesigns\blogcms\BlogCMS;

class Search extends AbstractWidget
{

    public function render()
    {
        $this->response->setVar('blog_url', $this->blog->url());
        $this->response->write('viewSearchWidget.tpl', 'Search');
    }

}