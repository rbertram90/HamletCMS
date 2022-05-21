<?php

namespace HamletCMS\Widgets\forms;

use rbwebdesigns\core\Form;

class ConfigureTwitterWidget extends Form
{
    protected $attributes = [
        "class" => "ui form"
    ];

    protected $fields = [
        "widget[heading]" => [
            "type"    => "text",
            "label"   => "Heading",
            "value" => "",
            "attributes" => [
                "id" => "widget[heading]"
            ]
        ],
        "widget[type]" => [
            "type" => "dropdown",
            "label" => "Type",
            "options" => [
                "timeline" => "Profile timeline",
                "list" => "Twitter 'List'",
            ],
            "value" => "",
            "attributes" => [
                "id" => "widget[type]"
            ]
        ],
        "widget[handle]" => [
            "type"    => "text",
            "label"   => "Twitter Handle",
            "value" => "",
            "attributes" => [
                "id" => "widget[handle]"
            ]
        ],
        "widget[list]" => [
            "type"    => "text",
            "label"   => "List ID",
            "value" => "",
            "attributes" => [
                "id" => "widget[list]"
            ],
            "conditions" => [
                "show" => [
                    "widget[type]" => "list"
                ]
            ]
        ],
        "widget[help]" => [
            "type"    => "markup",
            "markup"  => "<p class='ui info message'>Go to <a href='https://publish.twitter.com/' target='_blank'>https://publish.twitter.com/</a> to get the correct ID for your timeline by pasting the timeline URL (e.g. <code>https://twitter.com/lists/123412341234</code>) into the search box, and taking the ID from the provided code. The ID appears towards the end of the URL in the <code>href</code> attribute e.g. <span style='color:grey'>https://twitter.com/yourtwitterhandle/lists/</span><strong>listname-1234</strong>.</p>",
            "conditions" => [
                "show" => [
                    "widget[type]" => "list"
                ]
            ]
        ],
        "widget[limit]" => [
            "type"    => "number",
            "label"   => "Number of Tweets to show (1-20)",
            "value" => "10",
            "attributes" => [
                "id" => "widget[limit]",
                "min" => 1,
                "max" => 20,
            ]
        ],
    ];

    public function validate()
    {

    }

    public function submit()
    {

    }
}