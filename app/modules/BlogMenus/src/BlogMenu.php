<?php

namespace rbwebdesigns\HamletCMS\BlogMenus;

use rbwebdesigns\HamletCMS\HamletCMS;

class BlogMenu {
    public $id;
    public $name;
    public $blog_id;

    protected $items = null;

    public function items()
    {
        if (is_null($this->items)) {
            $itemsModel = HamletCMS::model('\rbwebdesigns\HamletCMS\BlogMenus\model\MenuItems');
            $this->items = $itemsModel->getByMenu($this->id);
        }
        return $this->items;
    }
    
}