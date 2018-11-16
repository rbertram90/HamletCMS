<?php

namespace rbwebdesigns\blogcms;

class Contributors
{
    public function onGenerateMenu($args)
    {
        if ($args['id'] == 'bloglist') {

            $link = new MenuLink();
            $link->url = BlogCMS::route('contributors.manage', [
                'BLOG_ID' => $args['blog']['id']
            ]);
            $link->text = 'Contributors';
            $args['menu']->addLink($link);
        }
    }
}