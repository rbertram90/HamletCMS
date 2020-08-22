<?php

namespace rbwebdesigns\HamletCMS\Widgets\forms;

use rbwebdesigns\core\Form;

class ConfigureAlsoTagged extends Form
{
    protected $attributes = [
        "class" => "ui form"
    ];

    protected $fields = [
        "widget[heading]" => [
            "type"    => "text",
            "label"   => "Heading",
            "value" => "Test",
            "attributes" => [
                "id" => "widget[heading]"
            ]
        ]
    ];

    public function validate()
    {

    }

    public function submit()
    {

    }
}