<?php

namespace rbwebdesigns\blogcms\BlogMenus\model;

use rbwebdesigns\core\model\RBFactory;

class MenuItems extends RBFactory {

    function __construct($modelManager)
    {
        $this->tableName = 'menuitems';

        $this->subClass = '\rbwebdesigns\blogcms\BlogMenus\BlogMenuItem';

        $this->fields = [
            'id' => 'number',
            'menu_id' => 'number',
            'text' => 'string',
            'type' => 'string',
            'link_target' => 'string',
            'new_window' => 'boolean'
        ];

        parent::__construct($modelManager);
    }

    public function getItemById($linkID)
    {
        return $this->get('*', ['id' => $linkID], null, null, false);
    }

    public function getByMenu($menuID)
    {
        return $this->get('*', ['menu_id' => $menuID]);
    }
}