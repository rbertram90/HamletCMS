<?php

namespace rbwebdesigns\blogcms\BlogMenus\widgets;

use rbwebdesigns\blogcms\Widgets\AbstractWidget;
use rbwebdesigns\blogcms\BlogCMS;

class MenuWidget extends AbstractWidget
{

    public function render()
    {   
        $menuID = $this->config()['menu'];

        $menusModel = BlogCMS::model('\rbwebdesigns\blogcms\BlogMenus\model\Menus');
        $this->response->setVar('menu', $menusModel->getMenuById($menuID));
        $this->response->write('menu.tpl', 'BlogMenus');
    }

}