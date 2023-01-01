<?php

namespace HamletCMS\VideoPost\controller;

use HamletCMS\BlogPosts\controller\AbstractPostType;
use HamletCMS\HamletCMS;

class VideoPost extends AbstractPostType
{
    public function create()
    {
        parent::create();
        
        HamletCMS::$hideActionsMenu = true;

        $this->response->write('videopost.tpl', 'VideoPost');
    }

    public function edit()
    {
        parent::edit();
        
        HamletCMS::$hideActionsMenu = true;

        $this->response->write('videopost.tpl', 'VideoPost');
    }
}