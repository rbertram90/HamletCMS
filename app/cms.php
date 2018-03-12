<?php
namespace rbwebdesigns\blogcms;

use rbwebdesigns\core\Session;
use rbwebdesigns\core\Request;
use rbwebdesigns\core\model\ModelManager;

/**
 * /app/cms.php
 * 
 * Keeps static global variables to be used everywhere
 * a multi-singleton factory, yey for design patterns!
 */
class BlogCMS
{
    protected static $session = null;
    protected static $request = null;
    protected static $response = null;
    protected static $config = [];
    // protected static $modelManager = null;

    /**
     * @return array
     */
    public static function config()
    {
        return self::$config;
    }

    public static function addToConfig($items)
    {
        self::$config = array_merge_recursive(self::$config, $items);
    }

    /**
     * @return rbwebdesigns\core\Session
     */
    public static function session()
    {
        if(is_null(self::$session)) {
            self::$session = new Session();
        }
        return self::$session;
    }

    /**
     * @return rbwebdesigns\core\Request
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
     * @return rbwebdesigns\blogcms\BlogCMSResponse
     */
    public static function response()
    {
        if(is_null(self::$response)) {
            self::$response = new BlogCMSResponse();
        }
        return self::$response;
    }

    /**
     * @return rbwebdesigns\core\model\ModelManager
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
}
