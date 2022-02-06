<?php
namespace HamletCMS;

use rbwebdesigns\core\Request;
use HamletCMS\Website\controller\Site;
use HamletCMS\Widgets\controller\WidgetsView;

// http://www.hamletcms.localhost/api/blogs/byCategory?category=General

/****************************************************************
  Website entrypoint
****************************************************************/

    $server_config = __DIR__ . '/hamlet.json';

    if (!file_exists($server_config)) {
        print "Cannot find Hamlet config - please run app/updatepublic.php to setup.";
        exit;
    }

    $server_config = json_decode(file_get_contents($server_config), true);
    $server_root = $server_config['application_directory'];
    
    // Include cms setup script
    require_once $server_root . '/app/setup.inc.php';

    $entrypoint = filter_input(INPUT_GET, 'p');

    // Handle API requests
    if ($entrypoint === 'api') {
        require_once $server_root . '/app/api_setup.php';
        exit;
    }

    // Handle CMS requests
    if ($entrypoint === 'cms') {
        if (filter_input(INPUT_GET, 'query') === 'install') {
            require_once $server_root . '/app/install.php';
            exit;
        }
        require_once $server_root . '/app/cms_setup.php';
        exit;
    }

/****************************************************************
  Route request
****************************************************************/
    
    $request = new Request([
        'defaultControllerName' => 'home'
    ]);
    $response = new HamletCMSResponse();

    // Add default stylesheet(s)
    $response->addStylesheet('/hamlet/css/semantic.css');

    // Add default script(s)
    $response->addScript('/hamlet/resources/js/jquery-1.8.0.min.js');
    $response->addScript('/hamlet/js/semantic.js');
    
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
        
        require $server_root . '/app/blog_setup.inc.php';
        exit;
    }

/****************************************************************
  Setup controller
****************************************************************/

    if ($action == 'widgets') {
        // @todo make this go through proper routing system
        $controller = new WidgetsView();
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