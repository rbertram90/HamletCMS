<?php
namespace rbwebdesigns\blogcms;

class LayoutPost
{
    public function onViewEditPost($args) {
        if ($args['type'] != 'layout') return;

        $controller = new LayoutPost\controller\LayoutPost();
        $controller->edit();
    }
}