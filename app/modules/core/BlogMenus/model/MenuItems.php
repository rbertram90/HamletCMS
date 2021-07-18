<?php

namespace HamletCMS\BlogMenus\model;

use rbwebdesigns\core\model\RBFactory;

class MenuItems extends RBFactory {

    function __construct($modelManager)
    {
        $this->tableName = 'menuitems';

        $this->subClass = '\HamletCMS\BlogMenus\BlogMenuItem';

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

    /**
     * @param int $linkID
     * 
     * @return \HamletCMS\BlogMenus\MenuItem[]
     */
    public function getItemById($linkID)
    {
        return $this->get('*', ['id' => $linkID], null, null, false);
    }

    /**
     * @param \HamletCMS\BlogMenus\Menu $menu
     * 
     * @return \HamletCMS\BlogMenus\MenuItem[]
     */
    public function getByMenu($menu)
    {
        $sort = $menu->sort == 'custom' ? 'weight': $menu->sort;
        return $this->get('*', ['menu_id' => $menu->id], $sort .' ASC');
    }

    /**
     * Update the weighting of remaining links in a menu after removing one
     * 
     * @param $linkToRemove
     */
    public function reWeightLinks($linkToRemove) {
        return $this->db->query('UPDATE menuitems SET weight = weight-1 WHERE weight > '. $linkToRemove->weight);
    }

}