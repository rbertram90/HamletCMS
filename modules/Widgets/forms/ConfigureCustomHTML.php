<?php

namespace HamletCMS\Widgets\forms;

use rbwebdesigns\core\form\Form;

class ConfigureCustomHTML extends Form
{
    protected array $attributes = [
        "class" => "ui form"
    ];

    protected array $fields = [
        "widget[heading]" => [
            "type"    => "text",
            "label"   => "Heading",
            "value" => "",
            "attributes" => [
                "id" => "widget[heading]"
            ]
        ],
        "widget[content]" => [
            "type"    => "memo",
            "label"   => "Content",
            "value" => "content",
            "attributes" => [
                "id" => "widget[content]"
            ]
        ]
    ];

    public function saveData() {}
}