<?php
namespace HamletCMS\BlogPosts\controller;

use HamletCMS\GenericController;
use HamletCMS\HamletCMS;

class PostSettings extends GenericController
{

    /** @var \HamletCMS\Blog\Blog */
    protected $blog;

    public function __construct()
    {
        parent::__construct();

        $this->modelPermissions = HamletCMS::model('\HamletCMS\Contributors\model\Permissions');
        $this->blog = HamletCMS::getActiveBlog();

        if (!$this->modelPermissions->userHasPermission('change_settings', $this->blog->id)) {
            $this->response->redirect('/', '403 Access Denied', 'error');
        }

        HamletCMS::$activeMenuLink = '/cms/settings/menu/'. $this->blog->id;
    }

    /**
     * Handles /settings/posts/<blogid>
     * Edit post display settings
     */
    public function posts()
    {
        if ($this->request->method() == 'POST') return $this->updatePostsSettings();

        $this->response->setVar('postConfig', $this->getPostConfig());
        $this->response->setVar('postTemplate', $this->getPostTeaserTemplate());
        $this->response->setVar('postFullTemplate', $this->getFullPostTemplate());
        $this->response->setVar('cardTemplate', $this->getPostCardTemplate());
        $this->response->setVar('blog', $this->blog);
        $this->response->addScript('/resources/ace/ace.js');
        $this->response->setTitle('Post Settings - ' . $this->blog->name);
        $this->response->write('settings.tpl', 'BlogPosts');
    }

    /**
     * Get the post settings config
     * 
     * @return array
     */
    protected function getPostConfig()
    {
        $config = $this->blog->config();

        if (isset($config['posts'])) {
            // Default values where needed
            if (!isset($config['posts']['postsperpage'])) $config['posts']['postsperpage'] = 5;
            if (!isset($config['posts']['postsummarylength'])) $config['posts']['postsummarylength'] = 200;
            if (!isset($config['posts']['listtype'])) $config['posts']['listtype'] = 'items';
            return $config['posts'];
        }
        else {
            // No posts config exists - send defaults
            return ['postsperpage' => 5, 'postsummarylength' => 200];
        }
    }

    /**
     * Get the Smarty full page post template contents
     * 
     * @return string
     */
    protected function getFullPostTemplate()
    {
        $customFullTemplate  = SERVER_PATH_BLOGS .'/'. $this->blog->id .'/templates/singlepost.tpl';
        $defaultFullTemplate = SERVER_MODULES_PATH .'/core/BlogView/src/templates/posts/singlepost.tpl';
        $postFullTemplate = file_exists($customFullTemplate) ? $customFullTemplate : $defaultFullTemplate;
        return file_get_contents($postFullTemplate);
    }

    /**
     * Get the Smarty teaser template contents
     * 
     * @return string
     */
    protected function getPostTeaserTemplate()
    {
        $customTemplateFile  = SERVER_PATH_BLOGS .'/'. $this->blog->id .'/templates/teaser.tpl';
        $defaultTemplateFile = SERVER_MODULES_PATH .'/core/BlogView/src/templates/posts/teaser.tpl';
        $postTemplate = file_exists($customTemplateFile) ? $customTemplateFile : $defaultTemplateFile;
        return file_get_contents($postTemplate);
    }

    /**
     * Get the Smarty post card template contents
     * 
     * @return string
     */
    protected function getPostCardTemplate()
    {
        $customTemplateFile  = SERVER_PATH_BLOGS .'/'. $this->blog->id .'/templates/card.tpl';
        $defaultTemplateFile = SERVER_MODULES_PATH .'/core/BlogView/src/templates/posts/card.tpl';
        $postTemplate = file_exists($customTemplateFile) ? $customTemplateFile : $defaultTemplateFile;
        return file_get_contents($postTemplate);
    }

    /**
     *  Update how posts are displayed on the blog
     */
    protected function updatePostsSettings()
    {
        $update = $this->blog->updateConfig([
            'posts' => [
                'postsperpage'      => $this->request->getInt('fld_postsperpage'),
                'postsummarylength' => $this->request->getInt('fld_postsummarylength'),
                'listtype'          => $this->request->getString('fld_listtype'),
                'loadtype'          => $this->request->getString('fld_loadtype'),
                'parentclasslist'   => $this->request->getString('fld_parentclasslist'),
            ]
        ]);

        if (!$update) {
            $this->response->redirect('/cms/settings/posts/'. $this->blog->id, 'Error saving to database', 'error');
        }
        
        if (!is_dir(SERVER_PATH_BLOGS .'/'. $this->blog->id .'/templates')) {
            mkdir(SERVER_PATH_BLOGS .'/'. $this->blog->id .'/templates');
        }

        $postTemplate = $this->request->get('fld_post_template');
        $update1 = file_put_contents(SERVER_PATH_BLOGS .'/'. $this->blog->id .'/templates/teaser.tpl', $postTemplate);

        $cardTemplate = $this->request->get('fld_card_template');
        $update2 = file_put_contents(SERVER_PATH_BLOGS .'/'. $this->blog->id .'/templates/card.tpl', $cardTemplate);

        $postFullTemplate = $this->request->get('fld_post_full_template');
        $update3 = file_put_contents(SERVER_PATH_BLOGS .'/'. $this->blog->id .'/templates/singlepost.tpl', $postFullTemplate);

        if ($update1 && $update2 && $update3) {
            HamletCMS::runHook('onPostSettingsUpdated', ['blog' => $this->blog]);
            $this->response->redirect('/cms/settings/posts/' . $this->blog->id, 'Post settings updated', 'success');
        }
        else {
            $this->response->redirect('/cms/settings/posts/' . $this->blog->id, 'Error writing template file', 'error');
        }
    }

}