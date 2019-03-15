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
     * @var \rbwebdesigns\blogcms\Contributors\model\Permissions
     */
    protected $modelPermissions;
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
        $this->modelPermissions = BlogCMS::model('\rbwebdesigns\blogcms\Contributors\model\Permissions');
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
        $arrayBlogConfig = jsonToArray(SERVER_PATH_BLOGS .'/'. $blog->id .'/template_config.json');
        
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
        
        if (file_exists($widgetSettingsFilePath) && filesize($widgetSettingsFilePath) > 0) {
            // Make sure the groups match the template settings
            $widgets = JSONhelper::JSONFileToArray($widgetSettingsFilePath);
            return $widgets;
            // $newWidgets = [];
/*
            $templateConfig = JSONHelper::JSONFileToArray(SERVER_PATH_BLOGS.'/' . $blogID . '/template_config.json');
            if (array_key_exists('Zones', $templateConfig)) {
                foreach ($templateConfig['Zones'] as $zone) {
                    if (array_key_exists($zone, $widgets)) {
                        $newWidgets[$zone] = $widgets[$zone];
                    }
                    else {
                        $newWidgets[$zone] = [];
                    }
                }
            }
*/
            // return $newWidgets;
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
            $defaultWidgetConfig = [];
            $templateConfig = JSONHelper::JSONFileToArray(SERVER_PATH_BLOGS.'/' . $blogID . '/template_config.json');
            
            if (array_key_exists('Zones', $templateConfig)) {
                foreach ($templateConfig['Zones'] as $zone) {
                    $defaultWidgetConfig[$zone] = [];
                }
            }
            else {
                $defaultWidgetConfig['header'] = [];
                $defaultWidgetConfig['footer'] = [];
            }

            // Currently left and right columns always exist!
            $defaultWidgetConfig['rightpanel'] = [];
            $defaultWidgetConfig['leftpanel'] = [];
            
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
    public function getInstalledWidgets()
    {
        $cachePath = BlogCMS::getCacheDirectory() .'/widgets.json';
        if (!file_exists($cachePath)) self::reloadWidgetCache();
        return JSONhelper::JSONFileToArray($cachePath);
    }
    
    /**
     * POST /cms/settings/widgets
     */
    protected function saveWidgets()
    {
        $configPath = SERVER_PATH_BLOGS . '/' . $this->blog->id . '/widgets.json';
        if (!file_exists($configPath)) die('Cannot find widget config file');
        $config = JSONhelper::JSONFileToArray($configPath);

        $templateConfigPath = SERVER_PATH_BLOGS .'/'. $this->blog->id .'/template_config.json';
        $templateConfig = JSONhelper::JSONFileToArray($templateConfigPath);
        if (array_key_exists('Zones', $templateConfig)) {
            $zones = $templateConfig['Zones'];
        }
        else {
            $zones = ['header', 'footer', 'rightpanel', 'leftpanel'];
        }
        
        // Clear all existing widgets
        foreach ($zones as $zone) {
            $config[$zone] = [];
        }

        $widgets = $this->request->get('widgets');

        foreach ($widgets as $zoneName => $zoneWidgets) {
            foreach ($zoneWidgets as $widgetType => $widgetConfig) {
                $config[$zoneName][$widgetType] = JSONhelper::jsonToArray($widgetConfig);
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
        if (!$widgetname = $this->request->getString('widget', false)) {
            die('Unable to continue - no widget found');
        }
        
        // Get all widgets cache
        $allWidgets = $this->getInstalledWidgets();

        if (!array_key_exists($widgetname, $allWidgets)) {
            die('Unable to continue - widget '. $widgetname .' not found');
        }
        
        // Get the configuration form for this widget
        $widgetConfig = $allWidgets[$widgetname];
        $configFormClass = $widgetConfig['form'];
        $form = new $configFormClass();
        $form->output(true);

        $this->request->isAjax = true;
    }
    

    /**
     * NEW widgets.json via modules code
     */
    public static function reloadWidgetCache()
    {
        $file = fopen(BlogCMS::getCacheDirectory() .'/widgets.json', 'w');
        $widgetCache = [];

        foreach (BlogCMS::$modules as $module) {
            $filePath = SERVER_MODULES_PATH .'/'. $module->key .'/widgets.json';
            if (file_exists($filePath)) {
                $widgets = JSONhelper::JSONFileToArray($filePath);
                foreach ($widgets as $widget) {
                    if (array_key_exists($widget['key'], $widgetCache)) {
                        print 'WARNING: Duplicate widget key "'. $permission['key'] .'" in '. $module->key.PHP_EOL;
                        continue;
                    }

                    $configPath = SERVER_MODULES_PATH .'/'. $module->key .'/src/widgets/'. $widget['key'] .'/config.json';
                    if (file_exists($configPath)) {
                        $widgetConfig = JSONHelper::JSONFileToArray($configPath);
                        $widget = array_merge($widget, $widgetConfig);
                    }
                    $widgetCache[$widget['key']] = $widget;

                    if (php_sapi_name() == "cli") {
                        print "INFO: Added widget - ". $widget['key'] .PHP_EOL;
                    }
                }
            }
        }

        fwrite($file, JSONHelper::arrayToJSON($widgetCache));
        fclose($file);
    }


}