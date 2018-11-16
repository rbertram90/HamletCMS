<?php

namespace rbwebdesigns\blogcms;

class BlogPosts
{
    public function onGenerateMenu($args)
    {
        if ($args['id'] == 'bloglist') {

            $link = new MenuLink();
            $link->url = BlogCMS::route('posts.manage', [
                'BLOG_ID' => $args['blog']['id']
            ]);
            $link->text = 'Manage posts';
            $args['menu']->addLink($link);

            $link = new MenuLink();
            $link->url = BlogCMS::route('posts.create', [
                'BLOG_ID' => $args['blog']['id']
            ]);
            $link->text = 'Create new post';
            $args['menu']->addLink($link);
        }
    }
}