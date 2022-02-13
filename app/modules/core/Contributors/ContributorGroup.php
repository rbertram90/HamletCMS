<?php

namespace HamletCMS\Contributors;

use HamletCMS\HamletCMS;

class ContributorGroup
{
    public $blog_id;
    public $id;
    public $name;
    public $description;
    public $data;
    public $locked;
    public $super;

    /**
     * @return bool
     *   True if this group has contributors assigned. False otherwise.
     */
    public function hasContributors() {
        return count(HamletCMS::model('contributors')->getByGroup($this->id)) > 0;
    }

}
