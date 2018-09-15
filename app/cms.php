<?php
namespace rbwebdesigns\blogcms;

use rbwebdesigns\core\Session;
use rbwebdesigns\core\Request;
use rbwebdesigns\core\model\ModelManager;
use rbwebdesigns\blogcms\Addon;
use rbwebdesigns\core\JSONHelper;

/**
 * /app/cms.php
 * 
 * Keeps static global variables to be used everywhere
 * a multi-singleton factory, yey for design patterns!
 */
class BlogCMS
{
    /**
     * @var \rbwebdesigns\core\Session
     */
    protected static $session = null;
    /**
     * @var \rbwebdesigns\core\Request
     */
    protected static $request = null;
    /**
     * @var \rbwebdesigns\core\Response
     */
    protected static $response = null;
    /**
     * @var array  parsed config files
     */
    protected static $config = [];
    /**
     * @var array  enabled addons
     */
    protected static $addons = [];
    // protected static $modelManager = null;

    /**
     * @var int  id field for the blog for which we are managing
     */
    public static $blogID = 0;
    /**
     * @var array|null  cache for the database row for the blog we're managing in the CMS
     */
    public static $blog = null;
    /**
     * @var string  key for which sub-menu link should be highlighted within the CMS
     */
    public static $activeMenuLink = '';
    /**
     * @var boolean  flag for if the user a contributor to the active blog
     */
    public static $userIsContributor = false;

    /**
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
     */
    public static function getActiveBlog()
    {
        if (self::$blog) return self::$blog;
        if (self::$blogID) {
            $blogsModel = self::model('\rbwebdesigns\blogcms\model\Blogs');
            self::$blog = $blogsModel->getBlogById(self::$blogID);
            return self::$blog;
        }
        return null;
    }

    /**
     * @return \rbwebdesigns\core\Session
     */
    public static function session()
    {
        if(is_null(self::$session)) {
            self::$session = new Session();
        }
        return self::$session;
    }

    /**
     * @return \rbwebdesigns\core\Request
     */
    public static function request()
    {
        if(is_null(self::$request)) {
            self::$request = new Request([
                'defaultControllerName' => 'blog'
            ]);
        }
        return self::$request;
    }

    /**
     * @return \rbwebdesigns\blogcms\BlogCMSResponse
     */
    public static function response()
    {
        if(is_null(self::$response)) {
            self::$response = new BlogCMSResponse();
        }
        return self::$response;
    }

    /**
     * @param string $modelName
     * 
     * @return \rbwebdesigns\core\model\ModelManager
     */
    public static function model($modelName)
    {
        if(!self::$config['database']['name']) {
            die('Database name not configured - see: /app/config/config.json');
        }

        $modelManager = ModelManager::getInstance(self::$config['database']['name']);

        if(!$modelManager->getDatabaseConnection()->isConnected()) {
            $modelManager->getDatabaseConnection()->connect(self::$config['database']['server'],
                self::$config['database']['name'],
                self::$config['database']['user'],
                self::$config['database']['password']);
        }

        return $modelManager->get($modelName);
    }

    /**
     * Include a class in the addons.
     * 
     * @param string $className
     */
    public static function registerAddon($directoryName)
    {
        if (array_key_exists($directoryName, self::$addons)) {
            // don't duplicate entries
            return;
        }

        self::$addons[$directoryName] = new Addon($directoryName);
    }

    /**
     * Run a hook
     * 
     * @param string $hookName
     * @param array  $parameters
     */
    public static function runHook($hookName, $parameters)
    {
        foreach (self::$addons as $addon) {
            if (!$addon->instance) continue;
            if (method_exists($addon->instance, $hookName)) {
                $addon->instance->$hookName($parameters);
            }
        }
    }

    /**
     * Transform a route into a URL
     * 
     * @param string $route
     *  Name of the route to fetch
     * 
     * @return string
     *  URL that corresponds to the route
     */
    public static function route($route, $data = [])
    {
        $routeCache = self::getCache('routes');

        if (!array_key_exists($route, $routeCache)) return false;

        if (array_key_exists('permissions', $routeCache[$route])) {
            // Check permissions
            $modelContributors = self::model('\rbwebdesigns\blogcms\model\Contributors');
            $granted = $modelContributors->userHasPermission(self::session()->currentUser['id'], self::$blogID, $routeCache[$route]['permissions']);
            if (!$granted) return false;
        }

        $url = $routeCache[$route]['path'];
        $url = str_replace('{BLOG_ID}', self::$blogID, $url);

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
        // todo
        $routeCache = self::getCache('routes');

        $requestPath = ['cms', self::request()->getControllerName()];

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
     * Get a cache by name - this will match the filename
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

        foreach (self::$addons as $addon) {
            if (file_exists(SERVER_ADDONS_PATH .'/'. $addon->key .'/menu.json')) {
                $links = JSONhelper::JSONFileToArray(SERVER_ADDONS_PATH .'/'. $addon->key .'/menu.json');
                foreach ($links as $link) {
                    if (!array_key_exists($link['menu'], $menuCache)) $menuCache[$link['menu']] = [];

                    if (array_key_exists($link['weight'], $menuCache[$link['menu']])) {
                        print 'WARNING: Duplicate menu weighting ('. $link['weight'] .') for '. $link['menu'] .' in '. $addon->key.PHP_EOL;
                        continue;
                    }

                    $menuCache[$link['menu']][$link['weight']] = array_diff_key($link, ['menu' => null, 'weight' => null]);

                    if (php_sapi_name() == "cli") {
                        print "INFO: Added link to ". $link['menu'] .PHP_EOL;
                    }
                }
            }
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

        foreach (self::$addons as $addon) {
            if (file_exists(SERVER_ADDONS_PATH .'/'. $addon->key .'/routes.json')) {
                $routes = JSONhelper::JSONFileToArray(SERVER_ADDONS_PATH .'/'. $addon->key .'/routes.json');
                foreach ($routes as $route) {
                    if (array_key_exists($route['key'], $routes)) {
                        print 'WARNING: Duplicate route key "'. $route['key'] .'" in '. $addon->key.PHP_EOL;
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

        foreach (self::$addons as $addon) {
            $filePath = SERVER_ADDONS_PATH .'/'. $addon->key .'/permissions.json';
            if (file_exists($filePath)) {
                $permissions = JSONhelper::JSONFileToArray($filePath);
                foreach ($permissions as $permission) {
                    if (array_key_exists($permission['key'], $permissions)) {
                        print 'WARNING: Duplicate permission key "'. $permission['key'] .'" in '. $addon->key.PHP_EOL;
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
    public static function generateTemplateCache()
    {
        $cacheDir = self::getCacheDirectory();

        $file = fopen($cacheDir .'/templates.json', 'w');
        $templatesCache = [];

        foreach (self::$addons as $addon) {
            $dirPath = SERVER_ADDONS_PATH .'/'. $addon->key .'/src/templates';
            if (file_exists($dirPath)) {
                $templatesCache[$addon->key] = $dirPath;
            }
        }

        fwrite($file, JSONHelper::arrayToJSON($templatesCache));
        fclose($file);
    }
    
}
