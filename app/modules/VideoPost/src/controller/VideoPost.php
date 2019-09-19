<?php

namespace rbwebdesigns\HamletCMS\VideoPost\controller;

use rbwebdesigns\HamletCMS\BlogPosts\controller\AbstractPostType;

class VideoPost extends AbstractPostType
{
    public function create()
    {
        parent::create();
        $this->response->write('videopost.tpl', 'VideoPost');
    }

    public function edit()
    {
        parent::edit();
        $this->response->write('videopost.tpl', 'VideoPost');
    }
}