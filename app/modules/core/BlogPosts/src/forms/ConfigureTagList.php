<?php

namespace HamletCMS\BlogPosts\forms;

use rbwebdesigns\core\Form;

class ConfigureTagList extends Form
{
    protected $attributes = [
        "class" => "ui form"
    ];

    protected $fields = [
        "widget[heading]" => [
            "type"  => "text",
            "label" => "Heading",
            "value" => "Tags",
            "attributes" => [
                "id" => "widget[heading]"
            ]
        ],
        "widget[numtoshow]" => [
            "type"  => "number",
            "label" => "Number of tags to show",
            "value" => "10",
            "attributes" => [
                "id" => "widget[numtoshow]"
            ]
        ],
        "widget[lowerlimit]" => [
            "type"  => "number",
            "label" => "Only show tags with at least this number of associated posts",
            "value" => "1",
            "attributes" => [
                "id" => "widget[lowerlimit]"
            ]
        ],
        "widget[display]" => [
            "type"  => "dropdown",
            "options" => [
                "list" => "List",
                "cloud" => "Cloud"
            ],
            "label" => "How should the tags be displayed?",
            "value" => "list",
            "attributes" => [
                "id" => "widget[display]"
            ]
        ],
        "widget[sort]" => [
            "type"  => "dropdown",
            "options" => [
                "text" => "Text",
                "count" => "Post count"
            ],
            "label" => "How should the tags be ordered?",
            "value" => "count",
            "attributes" => [
                "id" => "widget[sort]"
            ]
        ]
    ];

    public function validate() {}
    public function submit() {}
}