<?php

namespace rbwebdesigns\blogcms\BlogMenus;

use rbwebdesigns\blogcms\BlogCMS;

class BlogMenu {
    public $id;
    public $name;
    public $blog_id;

    protected $items = null;

    public function items()
    {
        if (is_null($this->items)) {
            $itemsModel = BlogCMS::model('\rbwebdesigns\blogcms\BlogMenus\model\MenuItems');
            $this->items = $itemsModel->getByMenu($this->id);
        }
        return $this->items;
    }
    
}