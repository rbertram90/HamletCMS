<?php

namespace rbwebdesigns\blogcms;

class Settings
{
    public function onGenerateMenu($args)
    {
        if ($args['id'] == 'bloglist') {

            $link = new MenuLink();
            $link->url = BlogCMS::route('settings.menu', [
                'BLOG_ID' => $args['blog']->id
            ]);
            $link->text = 'Settings';
            $args['menu']->addLink($link);
        }
    }
}