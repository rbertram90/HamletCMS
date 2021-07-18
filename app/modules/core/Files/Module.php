<?php

namespace HamletCMS\Files;

use HamletCMS\HamletCMS;
use HamletCMS\MenuLink;

class Module
{
    public function onGenerateMenu($args)
    {
        if ($args['id'] == 'bloglist') {

            $link = new MenuLink();
            $link->url = HamletCMS::route('files.manage', [
                'BLOG_ID' => $args['blog']->id
            ]);
            $link->text = 'Files';
            $args['menu']->addLink($link);
        }
    }
}