<?php
namespace rbwebdesigns\blogcms;
use Codeliner;
use Athens\CSRF;
use rbwebdesigns\core\Sanitize;

/****************************************************************
  Blog CMS Start Point
****************************************************************/
    
    // Load JSON config file
    // Note: cannot use core function to do this as hasn't been loaded
    // at this stage - chicken and egg situation
    $config = json_decode(file_get_contents(dirname(__file__) . '/../config/config.json'), true);
    
    define('IS_DEVELOPMENT', $config['environment']['development_mode']); // Flag for development
    
    define('SERVER_ROOT', $config['environment']['root_directory']);  // Absolute path to root folder
    define('SERVER_PUBLIC_PATH', SERVER_ROOT . '/app/public');        // Path to www folder
    define('SERVER_PATH_TEMPLATES', SERVER_ROOT . '/templates');      // Path to the blog templates folder
    define('SERVER_PATH_BLOGS', SERVER_PUBLIC_PATH . '/blogdata');    // Path to the blogs data
    define('SERVER_AVATAR_FOLDER', SERVER_PUBLIC_PATH . '/avatars');  // Path to the folder containing user avatars
    define('SERVER_PATH_WIDGETS', SERVER_ROOT . '/app/widgets');      // Path to installed widgets

    // Include cms setup script
    require_once SERVER_ROOT.'/app/setup.inc.php';

    // Make sure we're in the right timezone
    date_default_timezone_set($config['environment']['timezone']);

    // Store the configuration
    BlogCMS::addToConfig($config);


/****************************************************************
  Route request
****************************************************************/
    
    $request = BlogCMS::request();
    $response = BlogCMS::response();
    
    // Controller naming is important!
    // For simplicity, the code makes the following assumptions:
    //
    // For pages within the CMS Url path should be structured as:
    //   <controllerName>/<actionName>/<parameters>
    //
    // The url structure for blogs is slightly different
    //   /blogs/<blog_id>/<action>
    //
    // Controller file is created under /app/controller folder named:
    //   <controllerName>_controller.inc.php
    $controllerName = $request->getControllerName();
    
    // Check if we are in the CMS or viewing a blog
    if($controllerName == 'blogs') {
        // Viewing a blog
        
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

    // Get controller class file
    require_once SERVER_ROOT . '/app/controller/public_controller.inc.php';
    
    // Dynamically instantiate new class
    $controllerClassName = '\rbwebdesigns\blogcms\\PublicController';
    $controller = new $controllerClassName($request, $response);

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

    // Call the requested function
    $action = $request->getUrlParameter(0, 'home');
    ob_start();
    $controller->$action($request, $response);
    $response->setBody(ob_get_contents());
    ob_end_clean();
    
/****************************************************************
  Output Template
****************************************************************/

    // Run Template here
    $response->writeTemplate('public/wrapper.tpl');