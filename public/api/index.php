<?php
namespace rbwebdesigns\blogcms;

use rbwebdesigns\core\Request;
use rbwebdesigns\core\Response;
use rbwebdesigns\blogcms\API\controller\Api;

/****************************************************************
  Blog CMS API Start Point
****************************************************************/

    // Include cms setup script
    require_once __DIR__ . '/../../app/setup.inc.php';


/****************************************************************
  Route request
****************************************************************/
    
    BlogCMS::$function = 'api';

    $request = BlogCMS::request();
    $response = BlogCMS::response();
    

/****************************************************************
  Get content
****************************************************************/

    $route = BlogCMS::pathMatch();

    $errored = false;

    $routeIsValid = $route &&
        array_key_exists('controller', $route) &&
        array_key_exists('action', $route);

    if (!$routeIsValid) {
        $response->setBody('{ "success": false, "errorMessage": "API method not found" }');
        $response->code(404);
        $errored = true;
    }

    $permissionsModel = BlogCMS::model('\rbwebdesigns\blogcms\Contributors\model\Permissions');
    $blogModel = BlogCMS::model('\rbwebdesigns\blogcms\Blog\model\Blogs');

    $blogID = $request->getInt('blogID', false);

    if ($blogID && !$blog = $blogModel->getBlogById($blogID)) {
        $response->setBody('{ "success": false, "errorMessage": "Blog not found" }');
        $response->code(406);
        $errored = true;
    }

    // Check permissions
    if (array_key_exists('permissions', $route) && count($route['permissions'])) {
        foreach ($route['permissions'] as $permissionName) {
            if (!$permissionsModel->userHasPermission($permissionName, $blog->id) ) {
                $response->setBody('{ "success": false, "errorMessage": "Access denied" }');
                $response->code(403);
                $errored = true;
            }
        }
    }

    if (!$errored) {
        $controller = new $route['controller']();
        $action = $route['action'];
        $controller->$action();
    }
    
    // TBD how to handle CORS
    // $this->response->addHeader('Access-Control-Allow-Origin', '*');
    $response->addHeader('Content-Type', 'application/json');
    $response->writeBody();