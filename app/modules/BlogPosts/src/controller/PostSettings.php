<?php
namespace rbwebdesigns\blogcms\BlogPosts\controller;

use rbwebdesigns\blogcms\GenericController;
use rbwebdesigns\blogcms\BlogCMS;

class PostSettings extends GenericController
{

    public function __construct()
    {
        parent::__construct();

        $this->modelPermissions = BlogCMS::model('\rbwebdesigns\blogcms\Contributors\model\Permissions');

        $currentUser = BlogCMS::session()->currentUser;
        $this->blog = BlogCMS::getActiveBlog();

        if (!$this->modelPermissions->userHasPermission('change_settings', $this->blog->id)) {
            $this->response->redirect('/', '403 Access Denied', 'error');
        }

        BlogCMS::$activeMenuLink = '/cms/settings/menu/'. $this->blog->id;
    }

    /**
     * Handles /settings/posts/<blogid>
     * Edit post display settings
     */
    public function posts()
    {
        if ($this->request->method() == 'POST') return $this->action_updatePostsSettings();

        $postConfig = $this->blog->config();

        if (isset($postConfig['posts'])) {
            // Default values where needed
            if(!isset($postConfig['posts']['postsperpage'])) $postConfig['posts']['postsperpage'] = 5;
            if(!isset($postConfig['posts']['postsummarylength'])) $postConfig['posts']['postsummarylength'] = 200;
            $this->response->setVar('postConfig', $postConfig['posts']);
        }
        else {
            // No posts config exists - send defaults
            $this->response->setVar('postConfig', ['postsperpage' => 5, 'postsummarylength' => 200]);
        }

        $this->response->setVar('blog', $this->blog);
        $this->response->setTitle('Post Settings - ' . $this->blog->name);
        $this->response->write('posts.tpl', 'Settings');
    }

    /**
     *  Update how posts are displayed on the blog
     */
    public function action_updatePostsSettings()
    {
        $update = $this->blog->updateConfig([
            'posts' => [
                'dateformat'        => $this->request->getString('fld_dateformat'),
                'timeformat'        => $this->request->getString('fld_timeformat'),
                'postsperpage'      => $this->request->getInt('fld_postsperpage'),
                'allowcomments'     => $this->request->getInt('fld_commentapprove'),
                'postsummarylength' => $this->request->getInt('fld_postsummarylength'),
                'showtags'          => $this->request->getString('fld_showtags'),
                'dateprefix'        => $this->request->getString('fld_dateprefix'),
                'dateseperator'     => $this->request->getString('fld_dateseperator'),
                'datelocation'      => $this->request->getString('fld_datelocation'),
                'timelocation'      => $this->request->getString('fld_timelocation'),
                'showsocialicons'   => $this->request->getString('fld_showsocialicons'),
                'shownumcomments'   => $this->request->getString('fld_shownumcomments')
            ]
        ]);
        
        if($update) {
            BlogCMS::runHook('onPostSettingsUpdated', ['blog' => $this->blog]);
            $this->response->redirect('/cms/settings/posts/' . $this->blog->id, "Post settings updated", "success");
        }
        else {
            $this->response->redirect('/cms/settings/posts/' . $this->blog->id, "Error saving to database", "error");
        }
    }

}