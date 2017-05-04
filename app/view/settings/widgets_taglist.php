                                <?php
$const_blogcmsroot = CLIENT_ROOT_BLOGCMS;
$initJSON = str_replace("&#34;", "\"", $blog['widgetJSON']);
$arrayWidgetConfig = json_decode($initJSON, true);

echo <<<EOD
    <div class="crumbtrail">
        <a href="{$const_blogcmsroot}">Home</a><a href='{$const_blogcmsroot}/overview/{$blog['id']}'>{$blog['name']}</a><a href='{$const_blogcmsroot}/config/{$blog['id']}'>Settings</a><a href="{$const_blogcmsroot}/config/{$blog['id']}/widgets">Widgets</a><a>Taglist</a>
    </div>
EOD;

$formhelper = new rbwebdesigns\HTMLFormsTools(null);
$formJSON = '{
	"title": "Widget Settings - Taglist",
	"icon": "listview.png",
	"action": "'.$const_blogcmsroot.'/config/'.$blog['id'].'/widgets/taglist/submit",
	"submitbuttonlabel": "Update",
	"fields":
	[
		{
			"type": "text",
			"name": "widgetfld_taglist_name",
			"label": "Widget Title",
            "current": "[!data.name]"
		},
        {
            "type": "dropdown",
            "name": "widgetfld_taglist_display",
            "label": "Display",
            "current": "[!data.display]",
            "values":
            {
                "List with Counts": "list",
                "Cloud": "cloud"
            }
        },
        {
            "type": "dropdown",
            "name": "widgetfld_taglist_orderby",
            "label": "Sort by",
            "current": "[!data.orderby]",
            "values":
            {
                "Count": "count",
                "Name": "name"
            }
        },
		{
			"type": "text",
			"name": "widgetfld_taglist_numtoshow",
			"label": "Maximum number of tags to show",
            "current": "[!data.numtoshow]"
		},
		{
			"type": "text",
			"name": "widgetfld_taglist_lowerlimit",
			"label": "Minimum tag count to show",
            "current": "[!data.lowerlimit]"
		}
	]
}';
echo $formhelper->generateFromJSON($formJSON, $arrayWidgetConfig['taglist']);

?>
                            