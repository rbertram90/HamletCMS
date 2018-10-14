<?php
namespace rbwebdesigns\blogcms;

use Athens\CSRF;
use rbwebdesigns\core\Request;
use rbwebdesigns\blogcms\BlogCMSResponse;
use rbwebdesigns\blogcms\Website\controller\Site;

/****************************************************************
  Website Start point
****************************************************************/

    // Include cms setup script
    require_once __DIR__ . '/../setup.inc.php';


/****************************************************************
  Route request
****************************************************************/
    
    $request = new Request([
        'defaultControllerName' => 'home'
    ]);
    $response = new BlogCMSResponse();

    // Usually this would be the controller name
    // In this case we're keeping it simple with
    // single level urls e.g /about, /contact
    $action = $request->getControllerName();
    
    // Check if we are viewing a blog
    if($action == 'blogs') {
        // Viewing a blog
        define('CUSTOM_DOMAIN', false);

        // Get the ID from the URL (& remove)
        define('BLOG_KEY', $request->getUrlParameter(0));
        
        // Check key is somewhat valid
        if(strlen(BLOG_KEY) != 10 || !is_numeric(BLOG_KEY)) redirect('/notfound');
        
        require SERVER_ROOT . '/app/blog_setup.inc.php';
        
        // Exit here
        exit;
    }

/****************************************************************
  Setup controller
****************************************************************/

    $controller = new Site($request, $response);

/****************************************************************
  Get body content
****************************************************************/

    // Add default stylesheet(s)
    $response->addStylesheet('/css/semantic.css');
    $response->addStylesheet('/resources/css/header.css');
    $response->addStylesheet('/css/blogs_stylesheet.css');

    // Add default script(s)
    $response->addScript('/resources/js/jquery-1.8.0.min.js');
    $response->addScript('/js/semantic.js');
    $response->addScript('/resources/js/core-functions.js');
    $response->addScript('/resources/js/validate.js');
    $response->addScript('/resources/js/ajax.js');

    $response->setTitle('Default title');
    $response->setDescription('Default page description');

    ob_start();
    $controller->$action($request, $response);
    $response->setBody(ob_get_contents());
    ob_end_clean();
    
/****************************************************************
  Output Template
****************************************************************/

    // Run Template here
    $response->writeTemplate('wrapper.tpl', 'Website');