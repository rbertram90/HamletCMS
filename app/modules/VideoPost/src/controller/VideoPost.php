<?php

namespace rbwebdesigns\blogcms\VideoPost\controller;

use rbwebdesigns\blogcms\BlogPosts\controller\AbstractPostType;

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