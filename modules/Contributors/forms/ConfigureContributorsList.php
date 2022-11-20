<?php

namespace HamletCMS\Contributors\forms;

use rbwebdesigns\core\form\Form;

class ConfigureContributorsList extends Form
{
    protected array $attributes = [
        "class" => "ui form"
    ];

    protected array $fields = [
        "widget[heading]" => [
            "type"  => "text",
            "label" => "Heading",
            "value" => "Blog contributors",
            "attributes" => [
                "id" => "widget[heading]"
            ]
        ],
        "widget[columns]" => [
            "type"  => "dropdown",
            "options" => [
                "one" => "1",
                "two" => "2",
                "three" => "3",
                "four" => "4",
                "five" => "5",
                "six" => "6"
            ],
            "label" => "Number of columns",
            "value" => "3",
            "attributes" => [
                "id" => "widget[columns]"
            ]
        ]
    ];

    public function saveData() {}
}
