<?php

namespace HamletCMS\Search\forms;

use rbwebdesigns\core\form\Form;

class ConfigureSearch extends Form
{
    protected array $attributes = [
        "class" => "ui form"
    ];

    protected array $fields = [
        "widget[heading]" => [
            "type"  => "text",
            "label" => "Heading",
            "value" => "Search",
            "attributes" => [
                "id" => "widget[heading]"
            ]
        ],
        "widget[postsperpage]" => [
            "type"  => "number",
            "label" => "Number of posts to show per page",
            "value" => "10",
            "attributes" => [
                "id" => "widget[postsperpage]"
            ]
        ]
    ];

    public function saveData() {}
}