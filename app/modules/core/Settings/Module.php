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
            $link->url = HamletCMS::route('settings.menu', [
                'BLOG_ID' => $args['blog']->id
            ]);
            $link->text = 'Settings';
            $args['menu']->addLink($link);
        }
    }

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
}