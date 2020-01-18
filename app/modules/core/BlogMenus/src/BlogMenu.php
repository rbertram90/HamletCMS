<?php

namespace rbwebdesigns\HamletCMS\BlogMenus;

use rbwebdesigns\HamletCMS\HamletCMS;

class BlogMenu {

    /** @var int */
    public $id;

    /** @var string */
    public $name;

    /** @var int */
    public $blog_id;

    /** @var string text|custom */
    public $sort;

    protected $items = null;

    /**
     * Get all menu items associated with this menu
     * 
     * @return \rbwebdesigns\HamletCMS\BlogMenus\BlogMenuItem
     */
    public function items()
    {
        if (is_null($this->items)) {
            $itemsModel = HamletCMS::model('\rbwebdesigns\HamletCMS\BlogMenus\model\MenuItems');
            $this->items = $itemsModel->getByMenu($this);
        }
        return $this->items;
    }
    
}