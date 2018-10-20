<?php

namespace rbwebdesigns\blogcms\MarkdownPost\controller;

use rbwebdesigns\blogcms\BlogPosts\controller\AbstractPostType;

class MarkdownPost extends AbstractPostType
{
    public function create()
    {
        parent::create();
        $this->response->write('standardpost.tpl', 'MarkdownPost');
    }

    public function edit()
    {
        parent::edit();
        $this->response->write('standardpost.tpl', 'MarkdownPost');
    }
}