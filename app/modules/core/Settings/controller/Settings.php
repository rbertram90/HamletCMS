<?php
namespace HamletCMS\Settings\controller;

use HamletCMS\GenericController;
use HamletCMS\Contributors\model\ContributorGroups;
use HamletCMS\Menu;
use HamletCMS\HamletCMS;
use rbwebdesigns\core\Sanitize;
use rbwebdesigns\core\JSONhelper;
use rbwebdesigns\core\ImageUpload;

/**
 * The controller acts as the intermediatory between the
 * model (database) and the view. Any requests to the model are sent from
 * here rather than the view.
 */
class Settings extends GenericController
{
    /** @var \HamletCMS\Blog\model\Blogs */
    protected $modelBlogs;
    /** @var \HamletCMS\BlogPosts\model\Posts */
    protected $modelPosts;
    /** @var \HamletCMS\UserAccounts\model\UserAccounts */
    protected $modelUsers;
    /** @var \HamletCMS\Contributors\model\Contributors */
    protected $modelContributors;

    /** @var \rbwebdesigns\core\Request */
    protected $request;
    /** @var \rbwebdesigns\core\Response */
    protected $response;

    /** @var \HamletCMS\Blog\Blog Active blog */
    protected $blog = null;
    
    /**
     * Constructor for Settings controller
     */
    public function __construct()
    {
        $this->modelBlogs = HamletCMS::model('\HamletCMS\Blog\model\Blogs');
        $this->modelPermissions = HamletCMS::model('\HamletCMS\Contributors\model\Permissions');
        $this->modelPosts = HamletCMS::model('\HamletCMS\BlogPosts\model\Posts');
        $this->modelUsers = HamletCMS::model('\HamletCMS\UserAccounts\model\UserAccounts');

        $this->request = HamletCMS::request();
        $this->response = HamletCMS::response();

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
        $currentUser = HamletCMS::session()->currentUser;
        $this->blog = HamletCMS::getActiveBlog();
        $this->response->setVar('blog', $this->blog);

        $access = true;

        // Check the user is a contributor of the blog to begin with
        if (!HamletCMS::$userGroup) {
            $access = false;
        }
        elseif (!$this->modelPermissions->userHasPermission('change_settings', $this->blog->id)) {
            $access = false;
        }

        if (!$access) {
            $this->response->redirect('/', '403 Access Denied', 'error');
        }

        HamletCMS::$activeMenuLink = '/cms/settings/menu/'. $this->blog->id;
    }
    
    /**
     * Handles /settings/menu
     */
    public function menu()
    {
        $settingsMenu = new Menu('settings');

        HamletCMS::runHook('onGenerateMenu', ['id' => 'settings', 'menu' => &$settingsMenu]);

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
        if ($this->request->method() == 'POST') return $this->updateBlogGeneral();

        $config = $this->blog->config()['blog'] ?? [];
        $this->response->setVar('config', $config);
        $this->response->setVar('tagList', json_encode($this->modelPosts->getAllTagsByBlog($this->blog->id)));
        $this->response->setVar('categorylist', HamletCMS::config()['blogcategories']);

        $this->response->setTitle('General Settings - ' . $this->blog->name);
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
            $defaultTemplate = SERVER_MODULES_PATH .'/core/BlogView/templates/header.tpl';
            $this->response->setVar('headerTemplate', file_get_contents($defaultTemplate));
        }
        $this->response->addScript('/resources/ace/ace.js');
        $this->response->setTitle('Customise Blog Header - ' . $this->blog->name);
        $this->response->write('header.tpl', 'Settings');
    }

    /**
     * Handles /settings/footer/<blogid>
     */
    public function footer()
    {
        if ($this->request->method() == 'POST') return $this->updateFooter();
        
        $blogconfig = $this->blog->config();
        if (isset($blogconfig['footer'])) $this->response->setVar('blogconfig', $blogconfig['footer']);
        else $this->response->setVar('blogconfig', []);

        $footerTemplate = SERVER_PATH_BLOGS .'/'. $this->blog->id .'/templates/footer.tpl';
        if (file_exists($footerTemplate)) {
            $this->response->setVar('footerTemplate', file_get_contents($footerTemplate));
        }
        else {
            $defaultTemplate = SERVER_MODULES_PATH .'/core/BlogView/templates/footer.tpl';
            $this->response->setVar('footerTemplate', file_get_contents($defaultTemplate));
        }

        $this->response->addScript('/resources/ace/ace.js');
        $this->response->setTitle('Customise Blog Footer - ' . $this->blog->name);
        $this->response->write('footer.tpl', 'Settings');
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
    public function template()
    {
        if ($this->request->method() == 'POST') return $this->applyNewTemplate();

        $this->response->setVar('core_templates', $this->getTemplateList('core'));
        $this->response->setVar('addon_templates', $this->getTemplateList('addon'));

        $this->response->setVar('blog', $this->blog);
        $this->response->setTitle('Choose Template - ' . $this->blog->name);
        $this->response->write('template.tpl', 'Settings');
    }

    /**
     * @param string $type  "core" or "addon"
     * 
     * @return array
     */
    protected function getTemplateList($type) {
        if (!file_exists(SERVER_PATH_TEMPLATES . "/{$type}")) return [];
        $templateFolders = scandir(SERVER_PATH_TEMPLATES . "/{$type}");
        $templateData = [];

        foreach ($templateFolders as $folder) {
            if ($folder == '.' || $folder == '..') continue;

            $infoPath = SERVER_PATH_TEMPLATES . "/{$type}/{$folder}/info.json";
            if (!file_exists($infoPath)) continue;

            $templateData[] = JSONhelper::JSONFileToArray($infoPath) + [
                'id' => $folder
            ];
        }

        return $templateData;
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
            if (!isset($config['Zones'])) {
                $config['Zones'] = [
                    'header', 'footer', 'leftpanel', 'rightpanel'
                ];
            }
            if (!isset($config['Imports'])) {
                $config['Imports'] = [];
            }
            $this->response->setVar('config', $config);
        }
        else {
            $this->response->setVar('config', [
                'Zones' => [
                    'header', 'footer', 'leftpanel', 'rightpanel'
                ],
                'Layout' => [
                    'ColumnCount' => 2,
                    'PostsColumn' => 1
                ],
                'Includes' => [
                    'semantic-ui' => true
                ],
                'Imports' => []
            ]);
        }

        $this->response->setTitle('Template settings - '. $this->blog->name);
        $this->response->write('templateConfig.tpl', 'Settings');
    }

    /******************************************************************
        POST - Blog Settings
    ******************************************************************/
    
    /**
     * Run update for basic information of a blog
     */
    public function updateBlogGeneral()
    {
        // Data to save to DB
        $blogData = [
            'name'        => $this->request->getString('fld_blogname'),
            'description' => $this->request->getString('fld_blogdesc'),
            'visibility'  => $this->request->getString('fld_blogsecurity'),
            'category'    => $this->request->getString('fld_category'),
            'domain'      => $this->request->getString('fld_domain'),
        ];

        // Logo Upload
        $logo = $this->request->getFile('fld_logo');
        if (strlen($logo['tmp_name']) > 0) {
            $imageUpload = new ImageUpload($logo);
            $imageUpload->maxUploadSize = 100000; // 100 KB
            $fileType = $imageUpload->getFileExtention();
            try {
                $upload = $imageUpload->upload(SERVER_PATH_BLOGS .'/'. $this->blog->id, 'logo.'. $fileType);

                if ($upload) {
                    $blogData['logo'] = 'logo.'. $fileType;
                }
                else {
                    HamletCMS::session()::addMessage('Error while saving logo file', 'error');
                }
            }
            catch(\Exception $e) {
                HamletCMS::session()::addMessage($e->getMessage(), 'error');
            }
        }

        // Icon Upload
        $icon = $this->request->getFile('fld_favicon');
        if (strlen($icon['tmp_name']) > 0) {
            $imageUpload = new ImageUpload($icon);
            $imageUpload->maxUploadSize = 50000; // 50 KB
            $fileType = $imageUpload->getFileExtention();
            try {
                $upload = $imageUpload->upload(SERVER_PATH_BLOGS .'/'. $this->blog->id, 'icon.'. $fileType);

                if ($upload) {
                    $blogData['icon'] = 'icon.'. $fileType;
                }
                else {
                    HamletCMS::session()::addMessage('Error while saving icon file', 'error');
                }
            }
            catch(\Exception $e) {
                HamletCMS::session()::addMessage($e->getMessage(), 'error');
            }
        }

        // Update DB
        $update = $this->modelBlogs->update(['id' => $this->blog->id], $blogData);

        // Update Config.json
        $this->blog->updateConfig([
            'blog' => [
                'homepage_type' => $this->request->getString('fld_homepage_type', 'posts'),
                'homepage_post_id'  => $this->request->getInt('fld_homepage_post_id', 0),
                'homepage_tag_list' => $this->request->getString('fld_tag_sections')
            ]
        ]);

        if ($update) {
            HamletCMS::runHook('onBlogSettingsUpdated', ['blog' => $this->blog]);
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

        $zones = array_values($this->request->get('zones', []));
        $zones = array_filter($zones);
        array_push($zones, 'leftpanel', 'rightpanel');
        $zones = array_unique($zones);

        // var_dump($zones);
        // die();

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
        $config['Zones'] = $zones;


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
    protected function updateFooter()
    {
        $update = $this->blog->updateConfig([
            'footer' => [
                'background_image'         => $this->request->getString('fld_footerbackgroundimage'),
                'bg_image_post_horizontal' => $this->request->getString('fld_horizontalposition'),
                'bg_image_post_vertical'   => $this->request->getString('fld_veritcalposition')
            ]
        ]);

        if (!$update) $this->response->redirect('/cms/settings/footer/' . $this->blog->id, 'Updated failed', 'error');
        
        // Save template file
        $save = $this->saveTemplateFile('footer.tpl', $this->request->get('footer_template'));
        if ($save === FALSE) {
            $this->response->redirect('/cms/settings/footer/' . $this->blog->id, 'Unable to save footer template file', 'error');
        }

        HamletCMS::runHook('onFooterSettingsUpdated', ['blog' => $this->blog]);
        $this->response->redirect('/cms/settings/footer/' . $this->blog->id, 'Footer updated', 'success');
    }
    
    /**
     * Save smarty template for blog
     */
    protected function saveTemplateFile($fileName, $content)
    {
        $templatesDir = SERVER_PATH_BLOGS .'/'. $this->blog->id .'/templates';
        if (!file_exists($templatesDir)) {
            mkdir($templatesDir);
        }
        return file_put_contents($templatesDir .'/'. $fileName, $content);
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
                'bg_image_align_horizontal' => $this->request->getString('fld_horizontalalign')
            ]
        ]);
        
        // Check update worked
        if (!$update) $this->response->redirect('/cms/settings/header/' . $this->blog->id, 'Unable to save header config', 'error');

        // Save template file
        $save = $this->saveTemplateFile('header.tpl', $this->request->get('header_template'));
        if ($save === FALSE) {
            $this->response->redirect('/cms/settings/header/' . $this->blog->id, 'Unable to save header template file', 'error');
        }

        HamletCMS::runHook('onHeaderSettingsUpdated', ['blog' => $this->blog]);
        $this->response->redirect('/cms/settings/header/' . $this->blog->id, 'Header updated', 'success');
    }
        
    /**
     * Apply a completely new template from the predefined templates
     */
    protected function applyNewTemplate()
    {
        $template_id = $this->request->getString('template_id', false);
        $template_type = $this->request->getString('template_type', false);

        if (!$template_id || !$template_type) {
            $this->response->redirect('/cms/settings/template/' . $this->blog->id, 'Template not found', 'error');
        }

        $templateDirectory = SERVER_PATH_TEMPLATES . "/{$template_type}/{$template_id}";
        $blogDirectory = SERVER_PATH_BLOGS .'/'. $this->blog->id;
        if (!is_dir($templateDirectory)) {
            $this->response->redirect('/cms/settings/template/' . $this->blog->id, 'Template not found', 'error');
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
        $headerTemplate = $templateDirectory .'/header.tpl';
        if (file_exists($headerTemplate)) {
            if(!copy($headerTemplate, $blogDirectory .'/templates/header.tpl')) die('Failed to copy header.tpl');
        }

        // Delete the widgets.json (as columns may have changed and no way to tell what current template is)
        // maybe do this differently in future updates
        if (file_exists(SERVER_PATH_BLOGS . "/{$this->blog->id}/widgets.json")) {
            unlink(SERVER_PATH_BLOGS . "/{$this->blog->id}/widgets.json");
        }

        HamletCMS::runHook('onTemplateChanged', ['blog' => $this->blog]);
        $this->response->redirect('/cms/settings/templateConfig/' . $this->blog->id, 'Template changed', 'success');
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
            HamletCMS::runHook('onStylesheetUpdated', ['blog' => $this->blog]);
            $this->response->redirect("/cms/settings/stylesheet/{$this->blog->id}", "Stylesheet updated", "success");
        }
        else {
            $this->response->redirect("/cms/settings/stylesheet/{$this->blog->id}", "Update failed", "error");
        }
    }

}
