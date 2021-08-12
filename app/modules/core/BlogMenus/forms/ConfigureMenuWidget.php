<?php

namespace HamletCMS\BlogMenus\forms;

use rbwebdesigns\core\Form;
use HamletCMS\HamletCMS;

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
        ],
        "widget[orientation]" => [
            "type" => "dropdown",
            "label" => "Orientation",
            "options" => [
                "" => "Horizontal",
                "vertical fluid" => "Vertical",
            ],
            "value" => "",
            "attributes" => [
                "id" => "widget[orientation]"
            ]
        ]
    ];

    public function __construct()
    {
        $blog = HamletCMS::getActiveBlog();
        $menuModel = HamletCMS::model('\HamletCMS\BlogMenus\model\Menus');
        $menus = $menuModel->get('*', ['blog_id' => $blog->id]);
        foreach ($menus as $menu) {
            $this->fields['widget[menu]']['options'][$menu->id] = $menu->name;
        }
    }

    public function validate() {}
    public function submit() {}
}