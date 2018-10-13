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
        if ($args['key'] == 'userProfile') {
            $tempResponse = new BlogCMSResponse();
            $tempResponse->setVar('comments', $this->model->getCommentsByUser($args['user']['id'], 0));
            $args['content'] .= $tempResponse->write('recentcommentsbyuser.tpl', 'PostComments', false);
        }
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

    public function dashboardCounts($args)
    {
        $args['counts']['comments'] = $this->model->getCount(['blog_id' => $args['blogID']]);
    }

    public function dashboardPanels($args)
    {
        $tempResponse = new BlogCMSResponse();
        $tempResponse->setVar('blog', $args['blog']);
        $tempResponse->setVar('currentUser', BlogCMS::session()->currentUser);
        $tempResponse->setVar('comments', $this->model->getCommentsByBlog($args['blog']['id'], 5));
        $args['panels'][] = $tempResponse->write('recentcommentsbyblog.tpl', 'PostComments', false);
    }

    public function onGenerateMenu($args)
    {
        if ($args['id'] == 'cms_main_actions') {

            $link = new MenuLink();
            $link->url = '/cms/comments/all/'. BlogCMS::$blogID;
            $link->icon = 'comments outline';
            $link->text = 'Comments';
            $link->permissions = ['administer_comments'];
            
            if ($link->accessible()) {
                $args['menu']->addLink($link);
            }
        }

    }
}
