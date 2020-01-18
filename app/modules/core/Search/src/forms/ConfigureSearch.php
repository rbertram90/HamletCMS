<?php

namespace rbwebdesigns\HamletCMS\Search\forms;

use rbwebdesigns\core\Form;

class ConfigureSearch extends Form
{
    protected $attributes = [
        "class" => "ui form"
    ];

    protected $fields = [
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

    public function validate() {}
    public function submit() {}
}