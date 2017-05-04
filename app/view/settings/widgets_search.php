<?php
// Set the widget name here
$widget_name = 'search';
$widget_title = ucfirst($widget_name);

$const_blogcmsroot = CLIENT_ROOT_BLOGCMS;

$initJSON = str_replace("&#34;", "\"", $blog['widgetJSON']);
$arrayWidgetConfig = json_decode($initJSON, true);

echo <<<EOD
    <div class="crumbtrail">
        <a href="{$const_blogcmsroot}">Home</a><a href='{$const_blogcmsroot}/overview/{$blog['id']}'>{$blog['name']}</a><a href='{$const_blogcmsroot}/config/{$blog['id']}'>Settings</a><a href="{$const_blogcmsroot}/config/{$blog['id']}/widgets">Widgets</a><a>{$widget_title}</a>
    </div>
EOD;

$formhelper = new rbwebdesigns\HTMLFormsTools(null);
$formJSON = '{
	"title": "Widget Settings - '.$widget_title.'",
	"icon": "avatar.png",
	"action": "'.$const_blogcmsroot.'/config/'.$blog['id'].'/widgets/search/submit",
	"submitbuttonlabel": "Update",
	"fields":
	[
		{
			"type": "text",
			"name": "widgetfld_'.$widget_name.'_name",
			"label": "Widget Title",
            "current": "[!data.name]"
		}
	]
}';
echo $formhelper->generateFromJSON($formJSON, $arrayWidgetConfig[$widget_name]);

?>
                            