<?php

namespace rbwebdesigns\HamletCMS\BlogMenus\model;

use rbwebdesigns\core\model\RBFactory;

class Menus extends RBFactory {

    function __construct($modelManager)
    {
        $this->tableName = 'menus';

        $this->subClass = '\rbwebdesigns\HamletCMS\BlogMenus\BlogMenu';

        $this->fields = [
            'id' => 'number',
            'name' => 'string',
            'blog_id' => 'number',
        ];

        parent::__construct($modelManager);
    }


    public function getMenuById($menuID)
    {
        return $this->get('*', ['id' => $menuID], null, null, false);
    }
    
}