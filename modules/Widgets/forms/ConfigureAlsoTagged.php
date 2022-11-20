<?php

namespace HamletCMS\Widgets\forms;

use rbwebdesigns\core\form\Form;

class ConfigureAlsoTagged extends Form
{
    protected array $attributes = [
        "class" => "ui form"
    ];

    protected array $fields = [
        "widget[heading]" => [
            "type"    => "text",
            "label"   => "Heading",
            "value" => "Test",
            "attributes" => [
                "id" => "widget[heading]"
            ]
        ]
    ];

    public function saveData() {}
}