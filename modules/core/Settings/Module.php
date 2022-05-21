<?php

namespace HamletCMS\Settings;

use HamletCMS\HamletCMS;
use HamletCMS\MenuLink;

class Module
{
    
    public function onGenerateMenu($args)
    {
        if ($args['id'] == 'bloglist') {
            $link = new MenuLink();
            $link->text = 'Settings';
            $link->url = HamletCMS::route('settings.menu', [
                'BLOG_ID' => $args['blog']->id
            ]);
            if ($link->url) {
                $args['menu']->addLink($link);
            }
        }
    }
    
/*
    public function onReloadCache() {
        $this->generateTemplateCache();
    }

    public function generateTemplateCache() {
        $templates_dir_contents = scandir(SERVER_PATH_TEMPLATES);
        $templates = [];
        foreach ($templates_dir_contents as $folder) {
            if ($folder == '.' || $folder == '..') continue;
            if (is_dir(SERVER_PATH_TEMPLATES . '/' . $folder)) {
                $templates[] = [
                    'name' => $folder
                ];
            }
        }
    }
*/

    public function runUnitTests($args) {
        if ($args['context'] === 'blog') {
            $test = new tests\SettingsTests();
            $test->blogID = $args['blogID'];
            $test->run();
        }
    }

}
