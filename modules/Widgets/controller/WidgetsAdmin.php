<?php

namespace HamletCMS\Widgets\controller;

use HamletCMS\GenericController;
use HamletCMS\HamletCMS;
use rbwebdesigns\core\JSONHelper;

class WidgetsAdmin extends GenericController
{
    
    /**
     * @var \HamletCMS\Contributors\model\Permissions
     */
    protected $modelPermissions;

    /**
     * @var \HamletCMS\Blog\Blog Active blog
     */
    protected $blog = null;
    
    public function __construct()
    {
        // Initialise Models
        $this->modelPermissions = HamletCMS::model('permissions');
        parent::__construct();
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
        $this->blog = HamletCMS::getActiveBlog();

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

        HamletCMS::$activeMenuLink = HamletCMS::route('settings.menu');
    }

    /**
     * Handles /settings/widgets/<blogid>
     */
    public function manage()
    {
        if ($this->request->method() == 'POST') return $this->saveWidgets();
        
        $this->response->setTitle('Customise Widgets - '. $this->blog->name);
        $this->response->setVar('blog', $this->blog);

        $config = $this->getWidgetConfig($this->blog->id);
        $this->checkWidgetJSON($this->blog, $config);

        $this->response->headerIcon = 'sliders horizontal';
        $this->response->headerText = $this->blog->name . ': Widgets';

        $this->response->setBreadcrumbs([
            $this->blog->name => $this->blog->url(),
            'Settings' => HamletCMS::route('settings.menu'),
            'Widgets' => null,
        ]);

        $this->response->setVar('widgetconfig', $config);
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
            return array_merge(
                $this->getZonesFromTemplate($blogID),
                JSONhelper::JSONFileToArray($widgetSettingsFilePath)
            );
        }
        
        return $this->createWidgetSettingsFile($blogID);
    }

    /**
     * Get zones from template settings
     * 
     * @return string[]
     */
    protected function getZonesFromTemplate($blogID) {
        $templateConfig = JSONHelper::JSONFileToArray(SERVER_PATH_BLOGS.'/' . $blogID . '/template_config.json');
            
        if (array_key_exists('Zones', $templateConfig)) {
            $zones = [];
            foreach ($templateConfig['Zones'] as $zone) {
                $zones[$zone] = [];
            }
            return $zones;
        }

        return [];
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
            $templateZones = $this->getZonesFromTemplate($blogID);
            
            if (count($templateZones) > 0) {
                $defaultWidgetConfig = $this->getZonesFromTemplate($blogID);
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
        $cachePath = HamletCMS::getCacheDirectory() .'/widgets.json';
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

            if(!array_key_exists('header', $zones)) $zones[] = 'header';
            if(!array_key_exists('footer', $zones)) $zones[] = 'footer';
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
        HamletCMS::runHook('onWidgetsUpdated', ['blog' => $this->blog]);
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
        $file = fopen(HamletCMS::getCacheDirectory() .'/widgets.json', 'w');
        $widgetCache = [];

        foreach (HamletCMS::$modules as $module) {
            $folder = $module->core ? SERVER_MODULES_PATH : SERVER_ADDONS_PATH . '/modules';
            $filePath = "{$folder}/{$module->key}/widgets.json";
            if (file_exists($filePath)) {
                $widgets = JSONhelper::JSONFileToArray($filePath);
                foreach ($widgets as $widget) {
                    if (array_key_exists($widget['key'], $widgetCache)) {
                        print 'WARNING: Duplicate widget key "'. $widget['key'] .'" in '. $module->key.PHP_EOL;
                        continue;
                    }

                    $configPath = "{$folder}/{$module->key}/widgets/{$widget['key']}/config.json";
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