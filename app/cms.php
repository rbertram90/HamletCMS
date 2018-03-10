<?php
namespace rbwebdesigns\blogcms;

use rbwebdesigns\core\Session;
use rbwebdesigns\core\Request;
use rbwebdesigns\core\Response;

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

    public static function session() {
        if(is_null(self::$session)) {
            self::$session = new Session();
        }
        return self::$session;
    }

    public static function request() {
        if(is_null(self::$request)) {
            self::$request = new Request();
        }
        return self::$request;
    }

    public static function response() {
        if(is_null(self::$response)) {
            self::$response = new Response();
        }
        return self::$response;
    }
}
