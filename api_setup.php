<?php

namespace HamletCMS;

use rbwebdesigns\core\Request;

/****************************************************************
  HamletCMS API Start Point
****************************************************************/

    HamletCMS::$function = 'api';

    // Create custom request, shifting the query path along one.
    // First part of query variable is the controller name.
    $queryPath = filter_input(INPUT_GET, 'query');
    $queryParts = explode('/', $queryPath);
    $controllerName = $queryParts[0] ?? '';

    // Set the correct controller.
    $_REQUEST['p'] = $controllerName;

    // Set the correct query.
    if (count($queryParts) > 1) {
        $_REQUEST['query'] = implode('/', array_slice($queryParts, 1));
    }
    else {
        $_REQUEST['query'] = '';
    }
    
    $request = HamletCMS::request();
    $response = HamletCMS::response();

/****************************************************************
  Get content
****************************************************************/

    $route = HamletCMS::pathMatch();

    $routeIsValid = $route &&
        array_key_exists('controller', $route) &&
        array_key_exists('action', $route);

    if (!$routeIsValid) {
        $response->setBody('{ "success": false, "errorMessage": "API method not found" }');
        $response->code(404);
        $response->addHeader('Content-Type', 'application/json');
        $response->writeBody();
        exit;
    }

    $permissionsModel = HamletCMS::model('permissions');
    $blogModel = HamletCMS::model('blogs');

    $blogID = $request->getInt('blogID', false);

    if ($blogID && !$blog = $blogModel->getBlogById($blogID)) {
        $response->setBody('{ "success": false, "errorMessage": "Blog not found" }');
        $response->code(406);
        $response->addHeader('Content-Type', 'application/json');
        $response->writeBody();
        exit;
    }

    // Check permissions
    if (array_key_exists('permissions', $route) && count($route['permissions'])) {
        foreach ($route['permissions'] as $permissionName) {
            if (!$permissionsModel->userHasPermission($permissionName, $blog->id) ) {
                $response->setBody('{ "success": false, "errorMessage": "Access denied" }');
                $response->code(403);
                $response->addHeader('Content-Type', 'application/json');
                $response->writeBody();
                exit;
            }
        }
    }

    HamletCMS::$blogID = $blogID;

    $controller = new $route['controller']();
    $action = $route['action'];
    $controller->$action();
    
    // TBD how to handle CORS
    // $this->response->addHeader('Access-Control-Allow-Origin', '*');
    $response->addHeader('Content-Type', 'application/json');
    $response->writeBody();