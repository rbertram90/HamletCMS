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

        $customTemplateFile = SERVER_PATH_BLOGS .'/'. $this->blog->id .'/templates/teaser.tpl';
        if (file_exists($customTemplateFile)) {
            $this->response->setVar('postTemplate', file_get_contents($customTemplateFile));
        }
        else {
            $this->response->setVar('postTemplate', file_get_contents(SERVER_MODULES_PATH .'/BlogView/src/templates/posts/teaser.tpl'));
        }

        $customFullTemplate = SERVER_PATH_BLOGS .'/'. $this->blog->id .'/templates/singlepost.tpl';
        if (file_exists($customFullTemplate)) {
            $this->response->setVar('postFullTemplate', file_get_contents($customFullTemplate));
        }
        else {
            $this->response->setVar('postFullTemplate', file_get_contents(SERVER_MODULES_PATH .'/BlogView/src/templates/posts/singlepost.tpl'));
        }

        $this->response->addScript('/resources/ace/ace.js');
        $this->response->setVar('blog', $this->blog);
        $this->response->setTitle('Post Settings - ' . $this->blog->name);
        $this->response->write('settings.tpl', 'BlogPosts');
    }

    /**
     *  Update how posts are displayed on the blog
     */
    protected function action_updatePostsSettings()
    {
        $update = $this->blog->updateConfig([
            'posts' => [
                'postsperpage'      => $this->request->getInt('fld_postsperpage'),
                'allowcomments'     => $this->request->getInt('fld_commentapprove'),
                'postsummarylength' => $this->request->getInt('fld_postsummarylength'),
                'showtags'          => $this->request->getString('fld_showtags'),
                'showsocialicons'   => $this->request->getString('fld_showsocialicons'),
                'shownumcomments'   => $this->request->getString('fld_shownumcomments')
            ]
        ]);

        if (!$update) {
            $this->response->redirect('/cms/settings/posts/' . $this->blog->id, "Error saving to database", "error");
        }
        
        if (!is_dir(SERVER_PATH_BLOGS .'/'. $this->blog->id .'/templates')) {
            mkdir(SERVER_PATH_BLOGS .'/'. $this->blog->id .'/templates');
        }

        $postTemplate = $this->request->get('fld_post_template');
        $update = file_put_contents(SERVER_PATH_BLOGS .'/'. $this->blog->id .'/templates/teaser.tpl', $postTemplate);

        $postFullTemplate = $this->request->get('fld_post_full_template');
        $update = file_put_contents(SERVER_PATH_BLOGS .'/'. $this->blog->id .'/templates/singlepost.tpl', $postFullTemplate);

        if ($update) {
            BlogCMS::runHook('onPostSettingsUpdated', ['blog' => $this->blog]);
            $this->response->redirect('/cms/settings/posts/' . $this->blog->id, "Post settings updated", "success");
        }
        else {
            $this->response->redirect('/cms/settings/posts/' . $this->blog->id, "Error writing teaser template file", "error");
        }
    }

}