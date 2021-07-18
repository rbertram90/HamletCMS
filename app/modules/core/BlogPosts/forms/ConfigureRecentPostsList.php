<?php

namespace HamletCMS\BlogPosts\forms;

use rbwebdesigns\core\Form;

class ConfigureRecentPostsList extends Form
{
    protected $attributes = [
        "class" => "ui form"
    ];

    protected $fields = [
        "widget[heading]" => [
            "type"  => "text",
            "label" => "Heading",
            "value" => "Recent posts",
            "attributes" => [
                "id" => "widget[heading]"
            ]
        ],
        "widget[maxposts]" => [
            "type"  => "number",
            "label" => "Number of posts to show",
            "value" => "6",
            "attributes" => [
                "id" => "widget[maxposts]"
            ]
        ],
        "widget[style]" => [
            "type" => "dropdown",
            "label" => "List style",
            "options" => [
                "bullet_list" => "Bulleted list",
                "numbered_list" => "Numbered list",
                "divided_list" => "Divided list",
                "none" => "None",
            ],
            "value" => "",
            "attributes" => [
                "id" => "widget[style]"
            ]
        ]
    ];

    public function validate() {}
    public function submit() {}
}