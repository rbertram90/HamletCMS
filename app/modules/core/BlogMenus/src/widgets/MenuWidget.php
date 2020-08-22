<?php

namespace rbwebdesigns\HamletCMS\BlogMenus\widgets;

use rbwebdesigns\HamletCMS\Widgets\AbstractWidget;
use rbwebdesigns\HamletCMS\HamletCMS;

class MenuWidget extends AbstractWidget
{

    public function render()
    {   
        $menuID = $this->config()['menu'];

        if ($menuID) {
            $menusModel = HamletCMS::model('\rbwebdesigns\HamletCMS\BlogMenus\model\Menus');
            $this->response->setVar('menu', $menusModel->getMenuById($menuID));
            $this->response->write('menu.tpl', 'BlogMenus');
        }
    }

}