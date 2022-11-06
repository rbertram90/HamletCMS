<?php

namespace HamletCMS\MarkdownPost\controller;

use HamletCMS\BlogPosts\controller\AbstractPostType;
use HamletCMS\HamletCMS;

class MarkdownPost extends AbstractPostType
{
    
    public function create()
    {
        parent::create();

        HamletCMS::$hideActionsMenu = true;

        $this->response->write('standardpost.tpl', 'MarkdownPost');
    }

    public function edit()
    {
        parent::edit();

        HamletCMS::$hideActionsMenu = true;
        
        $this->response->write('standardpost.tpl', 'MarkdownPost');
    }

}
