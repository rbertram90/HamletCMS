<?php

namespace rbwebdesigns\HamletCMS\Contributors\widgets;

use rbwebdesigns\HamletCMS\Widgets\AbstractWidget;
use rbwebdesigns\HamletCMS\HamletCMS;

class ContributorsList extends AbstractWidget
{

    public function render() {
        $model = HamletCMS::model('\rbwebdesigns\HamletCMS\Contributors\model\Contributors');

        $this->response->setVar('contributors', $model->getBlogContributors($this->blog->id));
        $this->response->write('contributorsList.tpl', 'Contributors');
    }

}