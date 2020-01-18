<?php

namespace rbwebdesigns\HamletCMS\Contributors\forms;

use rbwebdesigns\core\Form;

class ConfigureContributorsList extends Form
{
    protected $attributes = [
        "class" => "ui form"
    ];

    protected $fields = [
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

    public function validate() {}
    public function submit() {}
}