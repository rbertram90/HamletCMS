<?php

namespace rbwebdesigns\blogcms\Widgets\controller;

use rbwebdesigns\blogcms\GenericController;
use rbwebdesigns\blogcms\Contributors\model\ContributorGroups;
use rbwebdesigns\blogcms\Menu;
use rbwebdesigns\blogcms\BlogCMS;
use rbwebdesigns\core\Sanitize;
use rbwebdesigns\core\JSONHelper;
use rbwebdesigns\core\HTMLFormTools;
use rbwebdesigns\core\AppSecurity;
use Codeliner\ArrayReader\ArrayReader;

class WidgetsAdmin extends GenericController
{
    /**
     * @var \rbwebdesigns\blogcms\Blog\model\Blogs
     */
    protected $modelBlogs;
    /**
     * @var \rbwebdesigns\blogcms\BlogPosts\model\Posts
     */
    protected $modelPosts;
    /**
     * @var \rbwebdesigns\blogcms\PostComments\model\Comments
     */
    protected $modelComments;
    /**
     * @var \rbwebdesigns\blogcms\UserAccounts\model\UserAccounts
     */
    protected $modelUsers;
    /**
     * @var \rbwebdesigns\blogcms\Contributors\model\Contributors
     */
    protected $modelContributors;
    /**
     * @var \rbwebdesigns\core\Request
     */
    protected $request;
    /**
     * @var \rbwebdesigns\core\Response
     */
    protected $response;
    /**
     * @var array Active blog
     */
    protected $blog = null;
    
    public function __construct()
    {
        // Initialise Models
        $this->modelBlogs = BlogCMS::model('\rbwebdesigns\blogcms\Blog\model\Blogs');
        $this->modelPermissions = BlogCMS::model('\rbwebdesigns\blogcms\Contributors\model\Permissions');
        $this->modelPosts = BlogCMS::model('\rbwebdesigns\blogcms\BlogPosts\model\Posts');
        $this->modelComments = BlogCMS::model('\rbwebdesigns\blogcms\PostComments\model\Comments');
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
     * Handles /settings/widgets/<blogid>
     */
    public function manage()
    {
        if ($this->request->method() == 'POST') return $this->saveWidgets();
        
        $this->response->setTitle('Customise Widgets - '. $this->blog->name);
        $this->response->setVar('blog', $this->blog);
        $this->response->setVar('widgetconfig', $this->getWidgetConfig($this->blog->id));
        $this->response->setVar('installedwidgets', $this->getInstalledWidgets());
        $this->response->write('widgets3.tpl', 'Widgets');
    }

    /**
     * Get some JSON which is okay to pass to the view - as widgets are dynamic if any are missing in the config
     * then we still want them to show in the options menu
     */
    protected function checkWidgetJSON($blog, &$widgetconfig)
    {
        $arrayBlogConfig = jsonToArray(SERVER_PATH_BLOGS.'/'.$blog->id.'/template_config.json');
        
        // Config
        //  -> Layout
        //     -> ColumnCount
        //  -> Header
        //  -> Footer
        //  -> (Leftpanel)
        //  -> (Rightpanel)
        
        $numcolumns = (array_key_exists('Layout', $arrayBlogConfig) && array_key_exists('ColumnCount', $arrayBlogConfig['Layout'])) ? $arrayBlogConfig['Layout']['ColumnCount'] : 2;
        
        if(!array_key_exists('header', $widgetconfig)) $widgetconfig['header'] = array();
        if(!array_key_exists('footer', $widgetconfig)) $widgetconfig['footer'] = array();
        
        switch($numcolumns) {
            case 3:
                // 2 Columns for widgets
                if(!array_key_exists('leftpanel', $widgetconfig)) $widgetconfig['leftpanel'] = array();
                if(!array_key_exists('rightpanel', $widgetconfig)) $widgetconfig['rightpanel'] = array();
                break;
                
            case 2:                
                $postscolumnnumber = (array_key_exists('Layout', $arrayBlogConfig) && array_key_exists('PostsColumn', $arrayBlogConfig['Layout'])) ? $arrayBlogConfig['Layout']['PostsColumn'] : 1;
                // 1 column for widgets
                if($postscolumnnumber == 2) {
                    if(!array_key_exists('leftpanel', $widgetconfig)) $widgetconfig['leftpanel'] = array();
                }
                else {
                    if(!array_key_exists('rightpanel', $widgetconfig)) $widgetconfig['rightpanel'] = array();
                }
                break;
                
            case 1:
                // Don't show any columns
                break;
        }
    }
    
    /**
     * Create the settings.json file
     * @param int blog ID
     * @return array containing widget settings
     */
    protected function getWidgetConfig($blogID)
    {
        $widgetSettingsFilePath = SERVER_PATH_BLOGS . '/' . $blogID . '/widgets.json';
        
        if(file_exists($widgetSettingsFilePath) && filesize($widgetSettingsFilePath) > 0) {
            return JSONHelper::JSONFileToArray($widgetSettingsFilePath);
        }
        
        return $this->createWidgetSettingsFile($blogID);
    }
    
    /**
     * Create the settings.json file
     * @param int blog ID
     * @return array containing default widget settings
     */
    protected function createWidgetSettingsFile($blogID)
    {
        if($widgetConfigFile = fopen(SERVER_PATH_BLOGS . '/' . $blogID . '/widgets.json', 'w'))
        {
            $defaultWidgetConfig = array('Header' => []);
            $templateConfig = JSONHelper::JSONFileToArray(SERVER_PATH_BLOGS.'/' . $blogID . '/template_config.json');
            $arrayReader = new ArrayReader($templateConfig);

            if($columnCount = $arrayReader->integerValue('Layout.ColumnCount')) {
                switch($columnCount) {
                    case 3:
                        $defaultWidgetConfig['RightPanel'] = [];
                        $defaultWidgetConfig['LeftPanel'] = [];
                        break;
                        
                    case 2:
                        if($postsColumn = $arrayReader->integerValue('Layout.PostsColumn')) {
                            if($postsColumn == 2) $defaultWidgetConfig['LeftPanel'] = [];
                            else $defaultWidgetConfig['RightPanel'] = [];
                        }
                        break;
                }
            }
            
            $defaultWidgetConfig['Footer'] = [];
            
            fwrite($widgetConfigFile, JSONhelper::arrayToJSON($defaultWidgetConfig));
            fclose($widgetConfigFile);
            return $defaultWidgetConfig;
        }
        
        die('Error: Unable to create widget settings - check folder permissions');
    }
    
    /**
     * @return array
     *   Details from all config.json files under the widgets directory
     */
    protected function getInstalledWidgets()
    {        
        $handle = opendir(SERVER_PATH_WIDGETS);
        $folders = array();
        
        // May be wise to create a cache for this...
        while ($file = readdir($handle)) {
            if (is_dir(SERVER_PATH_WIDGETS . '/' . $file) && $file != '.' && $file != '..') {
                $configPath = SERVER_PATH_WIDGETS . '/' . $file . '/config.json';
                if (!file_exists($configPath)) continue;
                $config = JSONhelper::JSONFileToArray($configPath);
                $folders[$file] = $config;
                $folders[$file]['_settings_json'] = JSONhelper::arrayToJSON($config['defaults']);
            }
        }
        
        return $folders;
    }
    
    /**
     * POST /cms/settings/widgets
     */
    protected function saveWidgets()
    {
        $configPath = SERVER_PATH_BLOGS . '/' . $this->blog->id . '/widgets.json';
        if (!file_exists($configPath)) die('Cannot find widget config file');
        $config = JSONhelper::JSONFileToArray($configPath);
        
        // Clear all existing widgets
        foreach ($config as $sectionName => $section) {
            $config[$sectionName] = [];
        }

        foreach ($_POST['widgets'] as $sectionName => $section) {
            foreach ($section as $widgettype => $widgetconfig) {
                $config[$sectionName][$widgettype] = JSONhelper::jsonToArray($widgetconfig);
            }
        }
        
        // Save JSON back to config file
        file_put_contents($configPath, JSONhelper::arrayToJSON($config));
        
        // View the widgets page
        BlogCMS::runHook('onWidgetsUpdated', ['blog' => $this->blog]);
        $this->response->redirect('/cms/settings/widgets/' . $this->blog->id, 'Widgets updated', 'success');
    }
    
    /**
     * Handles /settings/configurewidget/<blogid>
     */
    public function configurewidget()
    {
        if(!$widgetname = $this->request->getString('widget', false)) {
            die('Unable to continue - no widget found');
        }
        
        // Get the definition
        $formhelper = new HTMLFormTools(null);        
        $widgetConfigPath = SERVER_PATH_WIDGETS . '/' . $widgetname . '/config.json';
        
        // Get form definition
        if(!file_exists($widgetConfigPath)) die('Widget definition not found');
        $widgetConfig = JSONhelper::JSONFileToArray($widgetConfigPath);
                
        // Output form
        echo $formhelper->generateFromJSON($widgetConfig['form-configuration'], $widgetConfig['defaults']);
        
        $this->request->isAjax = true;
    }
    
}