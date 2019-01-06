<?php
namespace rbwebdesigns\blogcms\Settings\controller;

use rbwebdesigns\blogcms\GenericController;
use rbwebdesigns\blogcms\Contributors\model\ContributorGroups;
use rbwebdesigns\blogcms\Menu;
use rbwebdesigns\blogcms\BlogCMS;
use rbwebdesigns\core\Sanitize;
use rbwebdesigns\core\JSONHelper;

/**
 * The controller acts as the intermediatory between the
 * model (database) and the view. Any requests to the model are sent from
 * here rather than the view.
 */
class Settings extends GenericController
{
    /** @var \rbwebdesigns\blogcms\Blog\model\Blogs */
    protected $modelBlogs;
    /** @var \rbwebdesigns\blogcms\BlogPosts\model\Posts */
    protected $modelPosts;
    /** @var \rbwebdesigns\blogcms\UserAccounts\model\UserAccounts */
    protected $modelUsers;
    /** @var \rbwebdesigns\blogcms\Contributors\model\Contributors */
    protected $modelContributors;

    /** @var \rbwebdesigns\core\Request */
    protected $request;
    /** @var \rbwebdesigns\core\Response */
    protected $response;

    /** @var array Active blog */
    protected $blog = null;
    
    /**
     * Constructor for Settings controller
     */
    public function __construct()
    {
        $this->modelBlogs = BlogCMS::model('\rbwebdesigns\blogcms\Blog\model\Blogs');
        $this->modelPermissions = BlogCMS::model('\rbwebdesigns\blogcms\Contributors\model\Permissions');
        $this->modelPosts = BlogCMS::model('\rbwebdesigns\blogcms\BlogPosts\model\Posts');
        $this->modelUsers = BlogCMS::model('\rbwebdesigns\blogcms\UserAccounts\model\UserAccounts');

        $this->request = BlogCMS::request();
        $this->response = BlogCMS::response();

        $this->setup();
    }

    /**
     * Setup controller
     * 
     * 1. Gets the key records that will be used for any request to keep
     *    the code DRY (Blog)
     * 
     * 2. Checks the user has permissions to run the request
     */
    protected function setup()
    {
        $currentUser = BlogCMS::session()->currentUser;
        $this->blog = BlogCMS::getActiveBlog();
        $this->response->setVar('blog', $this->blog);

        $access = true;

        // Check the user is a contributor of the blog to begin with
        if (!BlogCMS::$userGroup) {
            $access = false;
        }
        elseif (!$this->modelPermissions->userHasPermission('change_settings', $this->blog->id)) {
            $access = false;
        }

        if (!$access) {
            $this->response->redirect('/', '403 Access Denied', 'error');
        }

        BlogCMS::$activeMenuLink = '/cms/settings/menu/'. $this->blog->id;
    }
    
    /**
     * Handles /settings/menu
     */
    public function menu()
    {
        $settingsMenu = new Menu('settings');

        BlogCMS::runHook('onGenerateMenu', ['id' => 'settings', 'menu' => &$settingsMenu]);

        $this->response->setVar('menu', $settingsMenu->getLinks());
        $this->response->setVar('blog', $this->blog);
        $this->response->setTitle('Blog Settings - ' . $this->blog->name);
        $this->response->write('menu.tpl', 'Settings');
    }

    /**
     * Handles /settings/general/<blogid>
     * Edit blog name, description etc.
     */
    public function general()
    {
        if ($this->request->method() == 'POST') return $this->action_updateBlogGeneral();

        $this->response->setVar('blog', $this->blog);
        $this->response->setTitle('General Settings - ' . $this->blog->name);
        $this->response->setVar('categorylist', BlogCMS::config()['blogcategories']);
        $this->response->write('general.tpl', 'Settings');
    }

    /**
     * Handles /settings/header/<blogid>
     */
    public function header()
    {
        if ($this->request->method() == 'POST') return $this->updateHeaderContent();
        
        $blogconfig = $this->blog->config();
        if (isset($blogconfig['header'])) $this->response->setVar('blogconfig', $blogconfig['header']);
        else $this->response->setVar('blogconfig', []);

        $headerTemplate = SERVER_PATH_BLOGS .'/'. $this->blog->id .'/templates/header.tpl';
        if (file_exists($headerTemplate)) {
            $this->response->setVar('headerTemplate', file_get_contents($headerTemplate));
        }
        else {
            $defaultTemplate = SERVER_MODULES_PATH .'/BlogView/src/templates/header.tpl';
            $this->response->setVar('headerTemplate', file_get_contents($defaultTemplate));
        }
        $this->response->addScript('/resources/ace/ace.js');
        $this->response->setVar('blog', $this->blog);
        $this->response->setTitle('Customise Blog Header - ' . $this->blog->name);
        $this->response->write('header.tpl', 'Settings');
    }

    /**
     * Handles /settings/footer/<blogid>
     */
    public function footer(&$request, &$response)
    {
        $blogID = $request->getUrlParameter(1);
        $blog = $this->modelBlogs->getBlogById($blogID);

        if ($request->method() == 'POST') return $this->action_updateFooterContent($request, $response, $blog);
        
        $blogconfig = $blog->config();
        if (isset($blogconfig['footer'])) $response->setVar('blogconfig', $blogconfig['footer']);
        else $response->setVar('blogconfig', []);

        $response->setVar('blog', $blog);
        $response->setTitle('Customise Blog Footer - ' . $blog->name);
        $response->write('footer.tpl', 'Settings');
    }

    /**
     * Handles /settings/pages/<blogid>
     */
    public function pages(&$request, &$response)
    {
        $blogID = $request->getUrlParameter(1);
        $blog = $this->modelBlogs->getBlogById($blogID);

        $action = $request->getUrlParameter(2);
        $actionTaken = true;

        switch ($action) {
            case 'add'    : $result = $this->action_addPage($request, $response, $blog); break;
            case 'up'     : $result = $this->action_movePageUp($request, $response, $blog); break;
            case 'down'   : $result = $this->action_movePageDown($request, $response, $blog); break;
            case 'remove' : $result = $this->action_removePage($request, $response, $blog); break;
            default: $actionTaken = false;
        }

        if($actionTaken && $result) {
            BlogCMS::runHook('onPageSettingsUpdated', ['blog' => $blog]);
            BlogCMS::Session()->addMessage('Page list updated', 'success');
        }
        elseif($actionTaken && !$result) {
            BlogCMS::Session()->addMessage('Failed to update page list', 'error');
        }

        $pagelist = explode(',', $blog->pagelist);
        $pages = $taglist = [];

        foreach ($pagelist as $postID) {
            if (is_numeric($postID)) {
                $pages[] = $this->modelPosts->get('*', ['id' => $postID], '', '', false);
            }
            elseif (substr($postID, 0, 2) == 't:') {
                $taglist[] = substr($postID, 2);
                $pages[] = $postID;
            }
        }
        
        $tags = $this->modelPosts->getAllTagsByBlog($blog->id);
        $posts = $this->modelPosts->get(['id', 'title'], ['blog_id' => $blog->id]);

        $response->setVar('pagelist', $pagelist);
        $response->setVar('pages', $pages);
        $response->setVar('taglist', $taglist);
        $response->setVar('tags', $tags);
        $response->setVar('posts', $posts);
        $response->setVar('blog', $blog);
        $response->setTitle('Manage Pages - ' . $blog->name);
        $response->write('pages.tpl', 'Settings');
    }

    /**
     * Handles /settings/stylesheet/<blogid>
     */
    public function stylesheet()
    {
        if($this->request->method() == 'POST') return $this->saveStylesheet();

        $this->response->addScript('/resources/ace/ace.js');
        $this->response->setVar('serverroot', SERVER_ROOT);
        $this->response->setTitle('Edit Stylesheet - ' . $this->blog->name);
        $this->response->write('stylesheet.tpl', 'Settings');
    }

    /**
     * Handles /settings/template/<blogid>
     */
    public function template(&$request, &$response)
    {
        $blogID = $request->getUrlParameter(1);
        $blog = $this->modelBlogs->getBlogById($blogID);

        if ($request->method() == 'POST') return $this->action_applyNewTemplate($request, $response, $blog);

        $response->setVar('blog', $blog);
        $response->setTitle('Choose Template - ' . $blog->name);
        $response->write('template.tpl', 'Settings');
    }
    
    /**
     * Handles /settings/templateConfig/<blogid>
     */
    public function templateConfig()
    {
        if ($this->request->method() == 'POST') return $this->saveTemplateConfig();

        $configFile = SERVER_PATH_BLOGS .'/'. $this->blog->id .'/template_config.json';
        if (file_exists($configFile)) {
            $config = JSONhelper::JSONFileToArray($configFile);
            $this->response->setVar('config', $config);
        }
        else {
            $this->response->setVar('config', [
                'Layout' => [
                    'ColumnCount' => 2,
                    'PostsColumn' => 1
                ],
                'Includes' => [
                    'semantic-ui' => true
                ]
            ]);
        }

        $this->response->setTitle('Template settings - '. $this->blog->name);
        $this->response->write('templateConfig.tpl', 'Settings');
    }

    /******************************************************************
        POST - Blog Settings
    ******************************************************************/
    
    /**
     * Run update for name and description of a blog
     */
    public function action_updateBlogGeneral()
    {
        $update = $this->modelBlogs->update(['id' => $this->blog->id], [
            'name'        => $this->request->getString('fld_blogname'),
            'description' => $this->request->getString('fld_blogdesc'),
            'visibility'  => $this->request->getString('fld_blogsecurity'),
            'category'    => $this->request->getString('fld_category'),
            'domain'      => $this->request->getString('fld_domain'),
        ]);

        if($update) {
            BlogCMS::runHook('onBlogSettingsUpdated', ['blog' => $this->blog]);
            $this->response->redirect('/cms/settings/general/'. $this->blog->id, "Blog settings updated", "success");
        }
        else {
            $this->response->redirect('/cms/settings/general/'. $this->blog->id, "Error saving to database", "error");
        }
    }

    /**
     * Save config for template
     */
    public function saveTemplateConfig()
    {
        $imports = array_values($this->request->get('imports', []));
        $imports = array_filter($imports);

        $newConfig = [
            'Layout' => [
                'ColumnCount' => $this->request->getInt('column_count', 2),
                'PostsColumn' => $this->request->getInt('post_column', 1)
            ]
        ];

        $configFile = SERVER_PATH_BLOGS .'/'. $this->blog->id .'/template_config.json';
        $oldConfig = JSONhelper::JSONFileToArray($configFile);

        $config = array_replace_recursive($oldConfig, $newConfig);

        $config['Imports'] = $imports;

        if (file_put_contents($configFile, JSONhelper::arrayToJSON($config))) {
            $this->response->redirect('/cms/settings/templateConfig/'. $this->blog->id, 'Template settings saved', 'success');
        }
        else {
            $this->response->redirect('/cms/settings/templateConfig/'. $this->blog->id, 'Error saving template settings', 'error');
        }
    }
    
    /**
     * Update the content in the footer
     */
    protected function action_updateFooterContent(&$request, &$response, $blog)
    {
        $update = $blog->updateConfig([
            'footer' => [
                'numcols'                  => $request->getString('fld_numcolumns'),
                'content_col1'             => $request->getString('fld_contentcol1'),
                'content_col2'             => $request->getString('fld_contentcol2'),
                'background_image'         => $request->getString('fld_footerbackgroundimage'),
                'bg_image_post_horizontal' => $request->getString('fld_horizontalposition'),
                'bg_image_post_vertical'   => $request->getString('fld_veritcalposition')
            ]
        ]);

        if(!$update) $response->redirect('/cms/settings/footer/' . $blog->id, 'Updated failed', 'error');
        
        BlogCMS::runHook('onFooterSettingsUpdated', ['blog' => $blog]);
        $response->redirect('/cms/settings/footer/' . $blog->id, 'Footer updated', 'success');
    }
    
    /**
     * Update the content in the header
     */
    protected function updateHeaderContent()
    {
        // Update config file
        $update = $this->blog->updateConfig([
            'header' => [
                'background_image'          => $this->request->getString('fld_headerbackgroundimage'),
                'bg_image_post_horizontal'  => $this->request->getString('fld_horizontalposition'),
                'bg_image_post_vertical'    => $this->request->getString('fld_veritcalposition'),
                'bg_image_align_horizontal' => $this->request->getString('fld_horizontalalign'),
                'hide_title'                => $this->request->getString('fld_hidetitle'),
                'hide_description'          => $this->request->getString('fld_hidedescription')
            ]
        ]);
        
        // Check update worked
        if (!$update) $this->response->redirect('/cms/settings/header/' . $this->blog->id, 'Unable to save header config', 'error');

        // Save template file
        $templatePath = SERVER_PATH_BLOGS .'/'. $this->blog->id .'/templates/header.tpl';
        $save = file_put_contents($templatePath, $this->request->get('header_template'));
        if ($save === FALSE) {
            $this->response->redirect('/cms/settings/header/' . $this->blog->id, 'Unable to save header template file', 'error');
        }

        BlogCMS::runHook('onHeaderSettingsUpdated', ['blog' => $this->blog]);
        $this->response->redirect('/cms/settings/header/' . $this->blog->id, 'Header updated', 'success');
    }
    
    /**
     * Add a page to the list of pages to show on menu
     * 
     * @param $request
     * @param $response
     * @param array $blog
     * 
     * @return bool Was the page added successfully?
     */
    protected function action_addPage(&$request, &$response, &$blog)
    {
        if (!$pageType = $request->getString('fld_pagetype', false)) return false;
        
        switch ($pageType) {
            case 'p':
                // Check that post we're adding is valid
                $newpageID = $request->getInt('fld_postid');
                $targetpost = $this->modelPosts->get(['blog_id'], ['id' => $newpageID], '', '', false);
                if (!$targetpost || $targetpost->blog_id != $blog->id) {
                    return false;
                }
                break;
                
            case 't':
                if (!$tag = $request->getString('fld_tag', false)) return false;
                $tag = str_replace(',', '', $tag);
                $newpageID = 't:' . $tag;
                break;

            default:
                return false;
        }

        // Update Current Page List
        if (array_key_exists('pagelist', $blog) && strlen($blog->pagelist) > 0) {
            $pagelist = $blog->pagelist . ',' . $newpageID;
        }
        else {
            $pagelist = $newpageID;
        }

        // Make sure we've got the most recent data for this request
        $blog->pagelist = $pagelist;
        
        return $this->modelBlogs->update(['id' => $blog->id], ['pagelist' => $pagelist]);
    }
    
    /**
     * @param $request
     * @param $response
     * @param array $blog
     * 
     * @return bool Was the page removed successfully?
     */
    protected function action_removePage(&$request, &$response, &$blog)
    {
        if (!$page = $request->getString('fld_postid', false)) return false;
        
        // Get position of page in the comma seperated list
        $pagelist = explode(',', $blog->pagelist);
        $idKey = array_search($page, $pagelist);
        if ($idKey === false) return false;

        // Remove page from list
        array_splice($pagelist, $idKey, 1);
        
        // Make sure we've got the most recent data for this request
        $blog->pagelist = implode(',', $pagelist);

        return $this->modelBlogs->update(['id' => $blog->id], [
            'pagelist' => $blog->pagelist
        ]);
    }
    
    /**
     * @param $request
     * @param $response
     * @param array $blog
     * 
     * @return bool Was the page moved successfully?
     */
    protected function action_movePageUp(&$request, &$response, &$blog)
    {
        if (!$page = $request->getString('fld_postid', false)) return false;
        
        $pagelist = explode(',', $blog->pagelist);
        $idKey = array_search($page, $pagelist);
        
        if ($idKey !== false && $idKey > 0) {
            $pagelist[$idKey] = $pagelist[$idKey - 1];
            $pagelist[$idKey - 1] = $page;
            $pagelist = implode(',', $pagelist);

            $blog->pagelist = $pagelist;

            return $this->modelBlogs->update(['id' => $blog->id], ['pagelist' => $pagelist]);
        }

        return false;
    }
    
    /**
     * @param $request
     * @param $response
     * @param array $blog
     * 
     * @return bool Was the page moved successfully?
     */
    protected function action_movePageDown(&$request, &$response, &$blog)
    {
        if (!$page = $request->getString('fld_postid', false)) return false;
        
        $pagelist = explode(',', $blog->pagelist);
        $idKey = array_search($page, $pagelist);
        
        if ($idKey !== false && $idKey < count($pagelist) - 1) {
            $pagelist[$idKey] = $pagelist[$idKey + 1];
            $pagelist[$idKey + 1] = $page;
            $pagelist = implode(',', $pagelist);
            
            $blog->pagelist = $pagelist;

            return $this->modelBlogs->update(['id' => $blog->id], ['pagelist' => $pagelist]);
        }
        
        return false;
    }
    
    /**
     * Apply a completely new template from the predefined templates
     */
    protected function action_applyNewTemplate($request, $response, $blog)
    {
        if (!$template_id = $request->getString('template_id', false)) {
            $response->redirect('/cms/settings/template/' . $blog->id, 'Template not found', 'error');
        }

        $templateDirectory = SERVER_PATH_TEMPLATES .'/'. $template_id;
        $blogDirectory = SERVER_PATH_BLOGS .'/'. $blog->id;
        if (!is_dir($templateDirectory)) {
            $response->redirect('/cms/settings/template/' . $blog->id, 'Template not found', 'error');
        }

        // Update default.css
        $copy_css = $templateDirectory .'/stylesheet.css';
        if (!copy($copy_css, $blogDirectory .'/default.css')) {
            die(showError('Failed to copy stylesheet.css'));
        }

        // Update template_config.json
        $copy_json = $templateDirectory .'/config.json';
        if (!copy($copy_json, $blogDirectory .'/template_config.json')) {
            die(showError('Failed to copy config.json'));
        }

        // Copy templates (if exist)
        if (!file_exists($blogDirectory .'/templates')) {
            mkdir($blogDirectory .'/templates');
        }
        $teaserTemplate = $templateDirectory .'/teaser.tpl';
        if (file_exists($teaserTemplate)) {
            if(!copy($teaserTemplate, $blogDirectory .'/templates/teaser.tpl')) die('Failed to copy teaser.tpl');
        }
        $fullTemplate = $templateDirectory .'/singlepost.tpl';
        if (file_exists($fullTemplate)) {
            if(!copy($fullTemplate, $blogDirectory .'/templates/singlepost.tpl')) die('Failed to copy singlepost.tpl');
        }

        // Delete the widgets.json (as columns may have changed and no way to tell what current template is)
        // maybe do this differently in future updates
        if (file_exists(SERVER_PATH_BLOGS . "/{$blog->id}/widgets.json")) {
            unlink(SERVER_PATH_BLOGS . "/{$blog->id}/widgets.json");
        }

        BlogCMS::runHook('onTemplateChanged', ['blog' => $blog]);
        $response->redirect('/cms/settings/template/' . $blog->id, 'Template changed', 'success');
    }
    
    /**
     * Save changes made to the stylesheet
     */
    protected function saveStylesheet()
    {
        // Sanitize Variables
        $css_string = strip_tags($this->request->get('fld_css'));

        if (is_dir(SERVER_PATH_BLOGS . "/{$this->blog->id}") &&
            file_put_contents(SERVER_PATH_BLOGS. "/{$this->blog->id}/default.css", $css_string)) {
            BlogCMS::runHook('onStylesheetUpdated', ['blog' => $this->blog]);
            $this->response->redirect("/cms/settings/stylesheet/{$this->blog->id}", "Stylesheet updated", "success");
        }
        else {
            $this->response->redirect("/cms/settings/stylesheet/{$this->blog->id}", "Update failed", "error");
        }
    }

}
