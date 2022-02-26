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
            if ($link->url) {
                $args['menu']->addLink($link);
            }
        }
    }

    public function imageUploader($args) {
        $args['tabs'][] = [
            'label' => 'Choose existing',
            'url' => '/cms/files/choosefile/' . $args['blog']->id,
        ];
    }
}