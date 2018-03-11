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
  Setup model
****************************************************************/
    
    $models = array(
        'users' => $GLOBALS['modelUsers']
    );


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
        
        // Location to blog index file
        $indexPath = SERVER_PATH_BLOGS . "/" . BLOG_KEY . "/default.php";
        
        // Check index file exists
        if(file_exists($indexPath)) require $indexPath;
        else redirect('/notfound');
        
        // Exit here
        exit;
    }

    if($controllerName == 'account') {
        $action = $request->getUrlParameter(0, 'login');

        require SERVER_ROOT . '/app/controller/account_controller.inc.php';
        $controller = new \rbwebdesigns\blogcms\AccountController();
        $controller->$action($request, $response);
        exit;
    }

    // User must be logged in to do anything in the CMS
    if(!USER_AUTHENTICATED) {
        $response->redirect('/account/login', 'Login required', 'error');
    }

    // Check form submissions for CSRF token
    CSRF::init();

/****************************************************************
  Setup View
****************************************************************/

    $view = new View($models);


/****************************************************************
  Setup controller
****************************************************************/
    
    // Check if we've got a valid controller
    $controllerFilePath = SERVER_ROOT . '/app/controller/' . $controllerName . '_controller.inc.php';

    if(!file_exists($controllerFilePath)) {
        $response->redirect('/', 'Page not found', 'error');
    }
    
    // Get controller class file
    require_once $controllerFilePath;
    
    // Dynamically instantiate new class
    $controllerClassName = '\rbwebdesigns\blogcms\\' . ucfirst($controllerName) . 'Controller';
    $controller = new $controllerClassName();

    // Call the requested function
    $action = $request->getUrlParameter(0, 'default');
    $controller->$action();

    // Cases where template not required
    if($controllerName == 'ajax' || $controllerName == 'api') {
        exit;
    }

/****************************************************************
  Additional site-specific file paths
****************************************************************/

    $css_includes = array(
        '/css/blogs_stylesheet'
    );
    
    $js_includes = array(
        '/js/sidemenu'
    );
    
    
/****************************************************************
  Default template content
****************************************************************/
    
    // Data Required for each page - Defaulted
    $DATA = array(
        'page_title' => 'Default Page Title',
        'page_description' => 'Default Page Description',
        'includes_css' => array_merge($global_css_includes, $css_includes),
        'includes_js' => array_merge($global_js_includes, $js_includes),
        'page_content' => '',
        'page_menu_actions' => ''
    );

    
/****************************************************************
  Generate final content
****************************************************************/
    
    // Try and get values from JSON
    if(array_key_exists('title', $endpoint)) $DATA['page_title'] = $endpoint['title'];
    if(array_key_exists('description', $endpoint)) $DATA['page_description'] = $endpoint['description'];
    
    // Set the side menu content
    $view->setSideMenu($controller->getSideMenu($queryParams, $action));
    
    // Title, description could also be dynamically assigned in this function call
    $controller->$endpoint['function']($queryParams);
