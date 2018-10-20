<?php
namespace rbwebdesigns\blogcms;

class MarkdownPost
{
    public function onViewEditPost($args) {
        if ($args['type'] != 'standard') return;

        $controller = new MarkdownPost\controller\MarkdownPost();
        $controller->edit();
    }
}