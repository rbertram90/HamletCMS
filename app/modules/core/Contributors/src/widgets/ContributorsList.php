<?php

namespace HamletCMS\Contributors\widgets;

use HamletCMS\Widgets\AbstractWidget;
use HamletCMS\HamletCMS;

class ContributorsList extends AbstractWidget
{

    public function render() {
        $model = HamletCMS::model('\HamletCMS\Contributors\model\Contributors');

        $this->response->setVar('contributors', $model->getBlogContributors($this->blog->id));
        $this->response->write('contributorsList.tpl', 'Contributors');
    }

}