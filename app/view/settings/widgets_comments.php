<?php

$const_blogcmsroot = CLIENT_ROOT_BLOGCMS;

$initJSON = str_replace("&#34;", "\"", $blog['widgetJSON']);
$arrayWidgetConfig = json_decode($initJSON, true);

echo <<<EOD
    <div class="crumbtrail">
        <a href="{$const_blogcmsroot}">Home</a><a href='{$const_blogcmsroot}/overview/{$blog['id']}'>{$blog['name']}</a><a href='{$const_blogcmsroot}/config/{$blog['id']}'>Settings</a><a href="{$const_blogcmsroot}/config/{$blog['id']}/widgets">Widgets</a><a>Comments</a>
    </div>
EOD;

$formhelper = new rbwebdesigns\HTMLFormsTools(null);
$formJSON = '{
	"title": "Widget Settings - Comments",
	"icon": "comment.png",
	"action": "'.$const_blogcmsroot.'/config/'.$blog['id'].'/widgets/comments/submit",
	"submitbuttonlabel": "Update",
	"fields":
	[
		{
			"type": "text",
			"name": "widgetfld_comments_name",
			"label": "Widget Title",
            "current": "[!data.name]"
		},
        {
			"type": "text",
			"name": "widgetfld_comments_numbertoshow",
			"label": "Number to Show",
            "current": "[!data.numbertoshow]"
        },
        {
			"type": "text",
			"name": "widgetfld_comments_maxlength",
			"label": "Maximum characters to show per message",
            "current": "[!data.maxlength]"
        }
	]
}';

echo $formhelper->generateFromJSON($formJSON, $arrayWidgetConfig['comments']);

?>
                            