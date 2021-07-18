<?php

namespace HamletCMS\BlogMenus\widgets;

use HamletCMS\Widgets\AbstractWidget;
use HamletCMS\HamletCMS;

class MenuWidget extends AbstractWidget
{

    public function render()
    {   
        $menuID = $this->config()['menu'];

        if ($menuID) {
            $menusModel = HamletCMS::model('\HamletCMS\BlogMenus\model\Menus');
            $this->response->setVar('menu', $menusModel->getMenuById($menuID));
            $this->response->write('menu.tpl', 'BlogMenus');
        }
    }

}