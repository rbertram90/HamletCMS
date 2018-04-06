<?php
namespace rbwebdesigns\blogcms;

use rbwebdesigns\core\Request;
use rbwebdesigns\core\Response;

/**
* Blog CMS API Start Point
*/
    // Include cms setup script
    require_once __DIR__ . '/../../setup.inc.php';

/****************************************************************
  Route request
****************************************************************/
    
    $request = new Request();
    $response = new Response();
    
/****************************************************************
  Setup controller
****************************************************************/

    // Get controller class file
    require_once SERVER_ROOT . '/app/controller/api_controller.inc.php';
    
    // Dynamically instantiate new class
    $controller = new \rbwebdesigns\blogcms\ApiController($request, $response);

/****************************************************************
  Get body content
****************************************************************/

    // Call the requested function
    $action = $request->getControllerName();

    if (method_exists($controller, $action)) {
        $controller->$action();
    }
    else {
        $controller->defaultAction();
    }
    