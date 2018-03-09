<?php
namespace rbwebdesigns\blogcms;
use Codeliner;
use Athens\CSRF;

/****************************************************************
  Blog CMS System Start Point
****************************************************************/
    
    // Load JSON config file
    // Note: cannot use core function to do this as hasn't been loaded
    // at this stage - chicken and egg situation
    $config = json_decode(file_get_contents(dirname(__file__) . '/../config/config.json'), true);
    
    // Flag for development
    define('IS_DEVELOPMENT', $config['environment']['development_mode']);

    // Absolute path to root folder
    define('SERVER_ROOT', $config['environment']['root_directory']);
    
    /* Server side relative paths */
    
    // Path to www folder
    define('SERVER_PUBLIC_PATH', SERVER_ROOT . '/app/public');
    
    // Path to the blog templates folder
    define('SERVER_PATH_TEMPLATES', SERVER_ROOT . '/templates');
    
    // Path to the blogs data
    define('SERVER_PATH_BLOGS', SERVER_PUBLIC_PATH . '/blogdata');
    
    // Path to the folder containing user avatars
    define('SERVER_AVATAR_FOLDER', SERVER_PUBLIC_PATH . '/avatars');

    define('SERVER_PATH_WIDGETS', SERVER_ROOT . '/app/widgets');

    // Include common setup script
    require_once SERVER_ROOT.'/app/setup.inc.php';

    // Make sure we're in the right timezone
    date_default_timezone_set($config['environment']['timezone']);


/****************************************************************
  Setup model
****************************************************************/
    
    $models = array(
        'users' => $GLOBALS['modelUsers']
    );


/****************************************************************
  Get Request Parameters
****************************************************************/
    
    // Get controller
    $action = isset($_GET['p']) ? safeString($_GET['p']) : -1;
    
    // Proccess Query String
    if(isset($_GET['query']))
    {
        $queryParams = strlen($_GET['query']) > 0 ? explode("/", $_GET['query']) : false;
    }
    else
    {
        $queryParams = false;
    }
    
    // Check if we are in the CMS or viewing a blog
    if($action == 'blogs' && strtolower(gettype($queryParams)) == 'array')
    {
        // Viewing a blog
        
        // Get the ID from the URL (& remove)
        define('BLOG_KEY', array_shift($queryParams));
        
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
    elseif($action == 'newuser')
    {
        // Sign up process
        require_once SERVER_ROOT.'/app/view/register.php';
        die();
    }
    elseif(!USER_AUTHENTICATED)
    {
        // Show login page
        require_once SERVER_ROOT.'/app/view/login.php';
        die();
    }


// $this->models['users']
//        $this->modelBlogs = new ClsBlog($cms_db);
//        $this->modelContributors = new ClsContributors($cms_db);
//        $this->modelPosts = new ClsPost($cms_db);
//        $this->modelComments = new ClsComment($cms_db);
//        $this->modelUsers = $GLOBALS['gClsUsers'];
//        $this->modelSecurity = new rbwebdesigns\AppSecurity();

    // Check form submissions for CSRF token
    CSRF::init();

/****************************************************************
  Setup View
****************************************************************/

    $view = new View($models);


/****************************************************************
  Setup controller
****************************************************************/
    
    // Handle Page Load
    if(($endpoint = $router->lookup($action)) === false) $endpoint = $router->lookup('default');
    
    // Get controller name specified in routes.json
    $controllerName = strtolower($endpoint['controller']);
    
    // Check if we've got a valid controller
    $controllerFilePath = SERVER_ROOT . '/app/controller/' . $controllerName . '_controller.inc.php';

    if(!file_exists($controllerFilePath)) die("Configuration error: Unable to load controler - {$controllerName} please check key {$action} in config/routes.json");
    
    // Get controller class file
    require_once $controllerFilePath;
    
    // Special cases for ajax/api requests
    if($controllerName == 'ajax')
    {
        $page_controller = new AjaxController($cms_db, $view, $queryParams);
        exit;
    }
    elseif($controllerName == 'api')
    {
        $page_controller = new ApiController($cms_db, $view, $queryParams);
        exit;
    }
    
    // Dynamically instantiate new class
    $controllerClassName = '\rbwebdesigns\blogcms\\' . ucfirst($controllerName) . 'Controller';
    $controller = new $controllerClassName($cms_db, $view);


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
    
?>