<?php

namespace rbwebdesigns\blogcms;
use rbwebdesigns;

$const_blogcmsroot = CLIENT_ROOT_BLOGCMS;

$initJSON = str_replace("&#34;", "\"", $blog['widgetJSON']);
$arrayWidgetConfig = json_decode($initJSON, true);

echo <<<EOD
    <div class="crumbtrail">
        <a href="{$const_blogcmsroot}">Home</a><a href='{$const_blogcmsroot}/overview/{$blog['id']}'>{$blog['name']}</a><a href='{$const_blogcmsroot}/config/{$blog['id']}'>Settings</a><a href="{$const_blogcmsroot}/config/{$blog['id']}/widgets">Widgets</a><a>Profile</a>
    </div>
EOD;

// Create form helper
$formhelper = new rbwebdesigns\HTMLFormsTools(null);

$formJSON = '{
    "title": "Widget Settings - Profile",
    "icon": "avatar.png",
    "action": "'.$const_blogcmsroot.'/config/'.$blog['id'].'/widgets/profile/submit",
    "submitbuttonlabel": "Update",
    "fields":
    [
        {
            "type": "text",
            "name": "widgetfld_profile_name",
            "label": "Widget Title",
            "current": "[!data.name]"
        },
        {
            "type": "yesno",
            "name": "widgetfld_profile_showpic",
            "label": "Show Profile Picture",
            "current": "[!data.showpic]"
        }
    ]
}';

echo $formhelper->generateFromJSON($formJSON, $arrayWidgetConfig['profile']);

?>
                            
                            
                            