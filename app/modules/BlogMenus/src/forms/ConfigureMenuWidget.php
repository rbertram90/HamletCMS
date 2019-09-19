<?php

namespace rbwebdesigns\HamletCMS\BlogMenus\forms;

use rbwebdesigns\core\Form;
use rbwebdesigns\HamletCMS\HamletCMS;

class ConfigureMenuWidget extends Form
{
    protected $attributes = [
        "class" => "ui form"
    ];

    protected $fields = [
        "widget[menu]" => [
            "type"  => "dropdown",
            "options" => [],
            "label" => "Menu",
            "value" => "count",
            "attributes" => [
                "id" => "widget[menu]"
            ]
        ]
    ];

    public function __construct()
    {
        $blog = HamletCMS::getActiveBlog();
        $menuModel = HamletCMS::model('\rbwebdesigns\HamletCMS\BlogMenus\model\Menus');
        $menus = $menuModel->get('*', ['blog_id' => $blog->id]);
        foreach ($menus as $menu) {
            $this->fields['widget[menu]']['options'][$menu->id] = $menu->name;
        }
    }

    public function validate() {}
    public function submit() {}
}