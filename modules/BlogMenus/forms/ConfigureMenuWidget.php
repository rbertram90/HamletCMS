<?php

namespace HamletCMS\BlogMenus\forms;

use rbwebdesigns\core\form\Form;
use HamletCMS\HamletCMS;

class ConfigureMenuWidget extends Form
{
    protected array $attributes = [
        "class" => "ui form"
    ];

    protected array $fields = [
        "widget[heading]" => [
            "label" => "Heading",
        ],
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
        ],
        "widget[colour]" => [
            "type" => "dropdown",
            "label" => "Colour",
            "options" => [
                "" => "White",
                "red inverted" => "Red",
                "orange inverted" => "Orange",
                "yellow inverted" => "Yellow",
                "olive inverted" => "Olive",
                "green inverted" => "Green",
                "teal inverted" => "Teal",
                "blue inverted" => "Blue",
                "violet inverted" => "Violet",
                "purple inverted" => "Purple",
                "pink inverted" => "Pink",
                "brown inverted" => "Brown",
                "grey inverted" => "Grey",
            ],
            "value" => "",
            "attributes" => [
                "id" => "widget[colour]"
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

    public function saveData() {}
}