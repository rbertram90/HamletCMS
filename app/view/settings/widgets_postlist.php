                                <?php

$const_blogcmsroot = CLIENT_ROOT_BLOGCMS;

$initJSON = str_replace("&#34;", "\"", $blog['widgetJSON']);
$arrayWidgetConfig = json_decode($initJSON, true);

echo <<<EOD
    <div class="crumbtrail">
        <a href="{$const_blogcmsroot}">Home</a><a href='{$const_blogcmsroot}/overview/{$blog['id']}'>{$blog['name']}</a><a href='{$const_blogcmsroot}/config/{$blog['id']}'>Settings</a><a href="{$const_blogcmsroot}/config/{$blog['id']}/widgets">Widgets</a><a>Postlist</a>
    </div>
EOD;

$formhelper = new rbwebdesigns\HTMLFormsTools(null);

$formJSON = '{
	"title": "Widget Settings - Postlist",
	"icon": "listview.png",
	"action": "'.$const_blogcmsroot.'/config/'.$blog['id'].'/widgets/postlist/submit",
	"submitbuttonlabel": "Update",
	"fields":
	[
		{
			"type": "text",
			"name": "widgetfld_postlist_name",
			"label": "Widget Title",
            "current": "[!data.name]"
		}
	]
}';
echo $formhelper->generateFromJSON($formJSON, $arrayWidgetConfig['postlist']);

?>
                            