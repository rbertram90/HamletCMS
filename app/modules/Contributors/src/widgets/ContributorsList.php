<?php

namespace rbwebdesigns\blogcms\Contributors\widgets;

use rbwebdesigns\blogcms\Widgets\AbstractWidget;
use rbwebdesigns\blogcms\BlogCMS;

class ContributorsList extends AbstractWidget
{

    public function render() {
        $model = BlogCMS::model('\rbwebdesigns\blogcms\Contributors\model\Contributors');

        $this->response->setVar('contributors', $model->getBlogContributors($this->blog->id));
        $this->response->write('contributorsList.tpl', 'Contributors');
    }

}