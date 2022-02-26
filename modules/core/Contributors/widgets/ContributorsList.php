<?php

namespace HamletCMS\Contributors\widgets;

use HamletCMS\Widgets\AbstractWidget;
use HamletCMS\HamletCMS;

class ContributorsList extends AbstractWidget
{

    public function render() {
        /** @var \HamletCMS\Contributors\model\Contributors */
        $model = HamletCMS::model('contributors');
        $contributorIds = $model->getBlogContributors($this->blog->id, true);
        $contributors = HamletCMS::model('useraccounts')->getByIds(array_column($contributorIds, 'user_id'));

        $this->response->setVar('contributors', $contributors);
        $this->response->write('contributorsList.tpl', 'Contributors');
    }

}