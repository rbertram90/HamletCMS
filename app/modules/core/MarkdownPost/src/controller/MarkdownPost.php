<?php

namespace rbwebdesigns\HamletCMS\MarkdownPost\controller;

use rbwebdesigns\HamletCMS\BlogPosts\controller\AbstractPostType;

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