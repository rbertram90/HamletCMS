<?php
namespace HamletCMS;

use rbwebdesigns\core\Session;
use rbwebdesigns\core\Request;
use rbwebdesigns\core\model\ModelManager;
use rbwebdesigns\core\JSONHelper;
use rbwebdesigns\core\ObjectDatabase;

/**
 * /app/cms.php
 * 
 * Keeps static global variables to be used everywhere
 * a multi-singleton factory, yey for design patterns!
 */
class HamletCMS
{
    /** @var \rbwebdesigns\core\Session */
    protected static $session = null;

    /** @var \rbwebdesigns\core\Request */
    protected static $request = null;

    /** @var \rbwebdesigns\core\Response */
    protected static $response = null;

    /** @var array  parsed config files */
    protected static $config = [];

    /** @var \HamletCMS\Module[]  enabled modules */
    public static $modules = [];

    /** @var \rbwebdesigns\core\model\ModelManager */
    protected static $modelManager = null;

    /** @var string[] aliases for model class */
    protected static $modelMapping = [];

    /** @var string */
    public static $function = 'cms';

    /** @var int  id field for the blog for which we are managing */
    public static $blogID = 0;

    /** @var \HamletCMS\Blog\Blog|null  cache for the database row for the blog we're managing in the CMS */
    public static $blog = null;

    /** @var \HamletCMS\BlogPosts\Post|null  cache for the database row for the post we're viewing */
    public static $post = null;

    /** @var string  key for which sub-menu link should be highlighted within the CMS */
    public static $activeMenuLink = '';

    /** @var boolean  flag for if the user a contributor to the active blog */
    public static $userGroup = false;

    /** @var boolean  flag to determine if the actions menu should not be generated */
    public static $hideActionsMenu = false;

    /**
     * Application configuration parsed from json file.
     * 
     * @return array
     */
    public static function config()
    {
        return self::$config;
    }

    /**
     * @param array $items
     */
    public static function addToConfig($items)
    {
        self::$config = array_merge_recursive(self::$config, $items);
    }

    /**
     * Get the active blog we're looking at
     * 
     * @return \HamletCMS\Blog\Blog
     */
    public static function getActiveBlog()
    {
        if (self::$blog) return self::$blog;
        if (self::$blogID) {
            $blogsModel = self::model('blogs');
            self::$blog = $blogsModel->getBlogById(self::$blogID);
            return self::$blog;
        }
        return null;
    }

    /**
     * Get the active post we're looking at
     * 
     * @return \HamletCMS\BlogPosts\Post
     */
    public static function getActivePost()
    {
        if (self::$post) return self::$post;
        $path = explode('/', self::$request->path());
        if (count($path) >= 2) {
            $slug = array_pop($path);
            $postCheck = array_pop($path);
            if ($postCheck === 'posts') {
                $postsModel = self::model('\HamletCMS\BlogPosts\model\Posts');
                self::$post = $postsModel->getPostByURL($slug, self::$blogID);
            }
        }
        return self::$post;
    }

    /**
     * @param mixed $instance
     *  Allow an alternative session object to be provided
     *  this is for testing purposes as we need to fake a
     *  request to inject data.
     * 
     * @return \rbwebdesigns\core\Session
     */
    public static function session($instance = null)
    {
        if (!is_null($instance)) {
            self::$session = $instance;
        }
        elseif (is_null(self::$session)) {
            self::$session = new Session();
        }
        return self::$session;
    }

    /**
     * @param mixed $instance
     *  Allow an alternative request object to be provided
     *  this is for testing purposes as we need to fake a
     *  request to inject data.
     * 
     * @return \rbwebdesigns\core\Request
     */
    public static function request($instance = null)
    {
        if (!is_null($instance)) {
            self::$request = $instance;
        }
        elseif (is_null(self::$request)) {
            self::$request = new Request([
                'defaultControllerName' => 'blog'
            ]);
        }
        return self::$request;
    }

    /**
     * @param mixed $instance
     *  Allow an alternative response object to be provided
     *  this is for testing purposes as we need to fake a
     *  request to inject data.
     * 
     * @return \HamletCMS\HamletCMSResponse
     */
    public static function response($instance = null)
    {
        if (!is_null($instance)) {
            self::$response = $instance;
        }
        elseif (is_null(self::$response)) {
            self::$response = new HamletCMSResponse();
        }
        return self::$response;
    }

    /**
     * @param string $modelName
     * 
     * @return mixed
     */
    public static function model($modelName)
    {
        if(!self::$config['database']['name']) {
            die('Database name not configured - see: /app/config/config.json');
        }

        // Establish connection
        self::databaseConnection();

        if (substr($modelName, 0, 1) !== '\\') {
            $modelCache = self::getCache('models');
            if (array_key_exists($modelName, $modelCache)) {
                $modelName = $modelCache[$modelName];
            }
        }

        return self::$modelManager->get($modelName);
    }

    /**
     * Get the model manager instance & establish connection if required
     */
    public static function databaseConnection()
    {
        // Model manager class only ever instantiated here on the first call
        if (!self::$modelManager) {
            self::$modelManager = ModelManager::getInstance(self::$config['database']['name']);

            // Now returning all SELECT results as objects
            self::$modelManager->setDatabaseClass(new ObjectDatabase());
        }

        if (!self::$modelManager->getDatabaseConnection()->isConnected()) {
            self::$modelManager->getDatabaseConnection()->connect(self::$config['database']['server'],
                self::$config['database']['name'],
                self::$config['database']['user'],
                self::$config['database']['password']);
        }

        return self::$modelManager->getDatabaseConnection();
    }

    /**
     * Include a class in the modules.
     * 
     * @param string $className
     */
    public static function registerModule($directoryName)
    {
        if (array_key_exists($directoryName, self::$modules)) {
            // don't duplicate entries
            return;
        }

        self::$modules[$directoryName] = new Module($directoryName);
    }

    /**
     * Get a module instance
     */
    public static function getModule($moduleName)
    {
        if (!array_key_exists($moduleName, self::$modules)) {
            return null;
        }

        return self::$modules[$moduleName];
    }

    /**
     * Run a hook
     * 
     * @param string $hookName
     * @param array  $parameters
     */
    public static function runHook($hookName, $parameters)
    {
        foreach (self::$modules as $module) {
            if (!$module->instance) continue;
            if (method_exists($module->instance, $hookName)) {
                $module->instance->$hookName($parameters);
            }
        }
    }

    /**
     * Transform a route into a URL. And run access check (sort of...)
     * 
     * @param string $route
     *   Name of the route to fetch.
     * @param mixed[] $data
     *   Replacement values for route parameters.
     * 
     * @return string|false
     *   URL path that corresponds to the route, false if access requirements
     *   have not been met.
     */
    public static function route($route, $data = [])
    {
        $routeCache = self::getCache('routes');
        if (!array_key_exists($route, $routeCache)) {
            return false;
        }
        $url = $routeCache[$route]['path'];

        // Check that if we've got the blog context, then the user
        // has permission to view/action this request.
        $blogID = $data['BLOG_ID'] ?? self::$blogID ?? false;

        if ($blogID) {
            if (array_key_exists('permissions', $routeCache[$route])) {
                // Check permissions
                $granted = self::model('permissions')
                    ->userHasPermission($routeCache[$route]['permissions'], $blogID);
                if (!$granted) return false;
            }
            $url = str_replace('{BLOG_ID}', $blogID, $url);
        }
        
        foreach ($data as $key => $var) {
            $key = strtoupper($key);
            $url = str_replace('{' . $key . '}', $var, $url);
        }

        return $url;
    }

    /**
     * Need a function that takes the current url path and finds the corresponding route
     */
    public static function pathMatch()
    {
        $routeCache = self::getCache('routes');
        if (!$routeCache) {
          print "Route cache not generated";
          exit;
        }
        $controllerName = self::request()->getControllerName();
        $isApi = substr($_SERVER['REQUEST_URI'], 0, 4) == '/api';

        if ($controllerName == 'blogs' && !$isApi) {
            $requestPath = ['blogs'];
        }
        else {
            $requestPath = [self::$function, $controllerName];
        }
        
        $e = 0;
        while ($elem = self::request()->getUrlParameter($e, false)) {
            $requestPath[] = $elem;
            $e++;
        }
        $pathCount = count($requestPath);

        foreach ($routeCache as $route) {
            $splitRoute = explode('/', $route['path']);
            array_shift($splitRoute);

            if (count($splitRoute) != $pathCount) continue;

            $match = true;
            for ($r = 0; $r < count($splitRoute); $r++) {
                if (substr($splitRoute[$r], 0, 1) == '{') {
                    continue;
                }
                if ($splitRoute[$r] != $requestPath[$r]) {
                    $match = false;
                    break;
                }
            }
            if ($match) {
                return $route;
            }
        }

        return false;
    }

    /**
     * Create cache folder if not already created.
     */
    public static function getCacheDirectory()
    {
        $dir = SERVER_ROOT .'/cache';

        if (!is_dir($dir)) {
            if (!mkdir($dir)) {
                die('Unable to create cache folder - please ensure the web server has write access to project root directory');
            }
        }

        return $dir;
    }

    /**
     * Get a cache by name
     * 
     * @param string $name
     *   Name of the cache - this will match the filename (without extention)
     * 
     * @return array
     *   Data from the cache
     */
    public static function getCache($name)
    {
        $cacheDir = self::getCacheDirectory();
        if (!file_exists($cacheDir .'/'. $name .'.json')) {
            return false;
        }

        return JSONhelper::JSONFileToArray($cacheDir .'/'. $name .'.json');
    }

    /**
     * Generate the menu links cache.
     */
    public static function generateMenuCache()
    {
        $cacheDir = self::getCacheDirectory();

        $file = fopen($cacheDir .'/menus.json', 'w');
        $menuCache = [];

        foreach (self::$modules as $module) {
            $folder = $module->core ? SERVER_MODULES_PATH : SERVER_ADDONS_PATH . '/modules';
            $jsonPath = "{$folder}/{$module->key}/menu.json";
            if ($links = JSONhelper::JSONFileToArray($jsonPath)) {
                foreach ($links as $link) {
                    if (!array_key_exists($link['menu'], $menuCache)) $menuCache[$link['menu']] = [];

                    $weight = intval($link['weight']);

                    if (array_key_exists($weight, $menuCache[$link['menu']])) {
                        print 'WARNING: Duplicate menu weighting ('. $weight .') for '. $link['menu'] .' in '. $module->key.PHP_EOL;
                        continue;
                    }

                    $menuCache[$link['menu']][$weight] = array_diff_key($link, ['menu' => null, 'weight' => null]);

                    if (php_sapi_name() == "cli") {
                        print "INFO: Added link to ". $link['menu'] .PHP_EOL;
                    }
                }
            }
        }

        foreach($menuCache as $key => $menu) {
            ksort($menuCache[$key]);
        }

        fwrite($file, JSONHelper::arrayToJSON($menuCache));
        fclose($file);
    }

    /**
     * Generate routes
     */
    public static function generateRouteCache()
    {
        // clear all routes
        // $modelManager = ModelManager::getInstance(self::$config['database']['name']);
        // $connection = $modelManager->getDatabaseConnection();

        $cacheDir = self::getCacheDirectory();

        $file = fopen($cacheDir .'/routes.json', 'w');
        $routeCache = [];

        foreach (self::$modules as $module) {
            $folder = $module->core ? SERVER_MODULES_PATH : SERVER_ADDONS_PATH . '/modules';
            if (file_exists("{$folder}/{$module->key}/routes.json")) {
                $routes = JSONhelper::JSONFileToArray("{$folder}/{$module->key}/routes.json");
                foreach ($routes as $route) {
                    if (array_key_exists($route['key'], $routes)) {
                        print 'WARNING: Duplicate route key "'. $route['key'] .'" in '. $module->key.PHP_EOL;
                        continue;
                    }
                    $routeCache[$route['key']] = $route;

                    if (php_sapi_name() == "cli") {
                        print "INFO: Added route - ". $route['key'] .PHP_EOL;
                    }
                }
            }
        }

        fwrite($file, JSONHelper::arrayToJSON($routeCache));
        fclose($file);
    }

    /**
     * Generate permissions
     * @todo a lot of this code is the same as routes - make a generic function
     */
    public static function generatePermissionCache()
    {
        $cacheDir = self::getCacheDirectory();

        $file = fopen($cacheDir .'/permissions.json', 'w');
        $permissionCache = [];

        foreach (self::$modules as $module) {
            $folder = $module->core ? SERVER_MODULES_PATH : SERVER_ADDONS_PATH . '/modules';
            $filePath = "{$folder}/{$module->key}/permissions.json";
            
            if (file_exists($filePath)) {
                $permissions = JSONhelper::JSONFileToArray($filePath);
                foreach ($permissions as $permission) {
                    if (array_key_exists($permission['key'], $permissionCache)) {
                        print 'WARNING: Duplicate permission key "'. $permission['key'] .'" in '. $module->key.PHP_EOL;
                        continue;
                    }
                    $permissionCache[$permission['key']] = $permission;

                    if (php_sapi_name() == "cli") {
                        print "INFO: Added permission - ". $permission['key'] .PHP_EOL;
                    }
                }
            }
        }

        fwrite($file, JSONHelper::arrayToJSON($permissionCache));
        fclose($file);
    }

    /**
     * Generate a list of all template directories for smarty
     */
    public static function generateSmartyTemplateCache()
    {
        $cacheDir = self::getCacheDirectory();

        $file = fopen($cacheDir .'/templates.json', 'w');
        $templatesCache = [];

        foreach (self::$modules as $module) {
            $folder = $module->core ? SERVER_MODULES_PATH : SERVER_ADDONS_PATH . '/modules';
            $dirPath = "{$folder}/{$module->key}/templates";
            if (file_exists($dirPath)) {
                $templatesCache[$module->key] = $dirPath;

                if (php_sapi_name() == "cli") {
                    print "INFO: Added template directory - ". $dirPath .PHP_EOL;
                }
            }
        }

        fwrite($file, JSONHelper::arrayToJSON($templatesCache));
        fclose($file);
    }

    /**
     * Generate list of model aliases
     */
    public static function generateModelAliasCache() {
        $cacheDir = self::getCacheDirectory();

        $file = fopen($cacheDir .'/models.json', 'w');
        $modelCache = [];

        foreach (self::$modules as $module) {
            $folder = $module->core ? SERVER_MODULES_PATH : SERVER_ADDONS_PATH . '/modules';
            $dirPath = "{$folder}/{$module->key}/model";
            $namespace = '\\HamletCMS\\' . $module->key . '\\model\\';
            if (file_exists($dirPath)) {
                foreach (scandir($dirPath) as $modelfile) {
                    $path_parts = pathinfo($modelfile);
                    if ($path_parts['extension'] = 'php') {
                        $class = $namespace . $path_parts['filename'];
                        if (class_exists($class) && isset($class::$alias)) {
                            $modelCache[$class::$alias] = $class;

                            if (php_sapi_name() == "cli") {
                                print "INFO: Added model alias - ". $class::$alias .PHP_EOL;
                            }
                        }
                    }
                }
            }
        }

        fwrite($file, JSONHelper::arrayToJSON($modelCache));
        fclose($file);
    }

}
