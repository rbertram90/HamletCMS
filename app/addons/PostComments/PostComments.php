<?php

namespace rbwebdesigns\blogcms;

class PostComments
{
    protected $model;

    public function __construct()
    {
        $this->model = BlogCMS::model('\rbwebdesigns\blogcms\PostComments\model\Comments');
    }

    public function content($args)
    {
        $tempResponse = new BlogCMSResponse();
        $tempResponse->setVar('comments', $this->model->getCommentsByUser($args['user']['id'], 0));
        $args['content'] .= $tempResponse->write('recentcomments.tpl', 'PostComments', false);
    }

    public function install()
    {
        // todo
        // create database
        // requires install process to be created first!!!!
    }

    public function uninstall()
    {
        // todo
        // delete database
        // requires uninstall process to be created first!!!!
    }
}
