<?php
namespace rbwebdesigns\blogcms;

use rbwebdesigns\core\Request;
use rbwebdesigns\blogcms\BlogCMSResponse;
use rbwebdesigns\blogcms\Website\controller\Site;


/****************************************************************
  Website entrypoint
****************************************************************/

    // Include cms setup script
    require_once __DIR__ .'/../app/setup.inc.php';


/****************************************************************
  Route request
****************************************************************/
    
    $request = new Request([
        'defaultControllerName' => 'home'
    ]);
    $response = new BlogCMSResponse();


    // Add default stylesheet(s)
    $response->addStylesheet('/css/semantic.css');
    // $response->addStylesheet('/css/blogs_stylesheet.css');

    // Add default script(s)
    $response->addScript('/resources/js/jquery-1.8.0.min.js');
    $response->addScript('/js/semantic.js');
    
    // Set default meta data
    $response->setTitle('Default title');
    $response->setDescription('Default page description');
    

    // Usually this would be the controller name
    // In this case we're keeping it simple with
    // single level urls e.g /about, /contact
    $action = $request->getControllerName();
    
    // Check if we are viewing a blog
    if ($action == 'blogs') {
        // Viewing a blog
        // Mark that we are not using a custom domain name
        define('CUSTOM_DOMAIN', false);

        // Get the blog ID from the URL
        define('BLOG_KEY', $request->getUrlParameter(0));
        
        // Check key is somewhat valid
        // @todo make a not found page!
        if(strlen(BLOG_KEY) != 10 || !is_numeric(BLOG_KEY)) {
            $response->redirect('/');
        }
        
        require SERVER_ROOT .'/app/blog_setup.inc.php';
        
        // Exit here
        exit;
    }

/****************************************************************
  Setup controller
****************************************************************/

    if ($action == 'widgets') {
        // @todo make this go through proper routing system
        $controller = new \rbwebdesigns\blogcms\Widgets\controller\WidgetsView($request, $response);
        $action = 'generateWidget';
        $applyTemplate = false;
    }
    else {
        $controller = new Site($request, $response);
        $applyTemplate = true;
    }

    // Check that the route exists
    if (!method_exists($controller, $action)) {
        $response->redirect('/');
    }

/****************************************************************
  Get body content
****************************************************************/

    ob_start();
    $controller->$action($request, $response);
    $response->setBody(ob_get_contents());
    ob_end_clean();
    
/****************************************************************
  Output Template
****************************************************************/

    // Run Template here
    if ($applyTemplate) {
        $response->writeTemplate('wrapper.tpl', 'Website');
    }
    else {
        $response->writeBody();
    }