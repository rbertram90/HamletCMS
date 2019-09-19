<?php

namespace rbwebdesigns\HamletCMS;

class Settings
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
}