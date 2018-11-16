<?php

namespace rbwebdesigns\blogcms;

class Files
{
    public function onGenerateMenu($args)
    {
        if ($args['id'] == 'bloglist') {

            $link = new MenuLink();
            $link->url = BlogCMS::route('files.manage', [
                'BLOG_ID' => $args['blog']['id']
            ]);
            $link->text = 'Files';
            $args['menu']->addLink($link);
        }
    }
}