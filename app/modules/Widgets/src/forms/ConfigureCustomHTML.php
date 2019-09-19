<?php

namespace rbwebdesigns\HamletCMS\Widgets\forms;

use rbwebdesigns\core\Form;

class ConfigureCustomHTML extends Form
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

    public function validate()
    {

    }

    public function submit()
    {

    }
}