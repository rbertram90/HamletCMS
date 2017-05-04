<?php
namespace rbwebdesigns\blogcms;
use Codeliner;

/****************************************************************
  Blog CMS System Start Point
****************************************************************/
    
    if(strpos($_SERVER['HTTP_HOST'], 'dev') === FALSE)
    {
        // Live system
        define('IS_DEVELOPMENT', false);
        
        // Absolute path to root folder
        define('SERVER_ROOT', '/home/ichiban/public_html/blogcms');
    }
    else
    {
        // Development
        define('IS_DEVELOPMENT', true);
        
        // Absolute path to root folder
        define('SERVER_ROOT', 'C:/xampp_5.6.24/htdocs/rbwebdesigns/projects/blog_cms');
    }
    
    /* Relative paths */
    
    // Path to www folder
    define('SERVER_PATH_WWW_ROOT', SERVER_ROOT . '/app/www_root');
    
    // Path to the templates folder
    define('SERVER_PATH_TEMPLATES', SERVER_ROOT . '/templates');
    
    // Path to the blogs
    define('SERVER_PATH_BLOGS', SERVER_PATH_WWW_ROOT . '/blogdata');
    
    define('SERVER_AVATAR_FOLDER', SERVER_PATH_WWW_ROOT . '/avatars');

    // Include common setup script
    require_once SERVER_ROOT.'/app/setup.inc.php';

    // Make sure we're in UK time
    date_default_timezone_set('Europe/London');


/****************************************************************
  Get Request Parameters
****************************************************************/
    
    // Get controller
    $controllerName = isset($_GET['p']) ? safeString($_GET['p']) : -1;
    
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
    if($controllerName == 'blogs' && strtolower(gettype($queryParams)) == 'array')
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
    elseif($controllerName == 'newuser')
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


/****************************************************************
  Setup model
****************************************************************/
    
    $models = array(
        'users' => $GLOBALS['modelUsers']
    );

// $this->models['users']
//        $this->modelBlogs = new ClsBlog($cms_db);
//        $this->modelContributors = new ClsContributors($cms_db);
//        $this->modelPosts = new ClsPost($cms_db);
//        $this->modelComments = new ClsComment($cms_db);
//        $this->modelUsers = $GLOBALS['gClsUsers'];
//        $this->modelSecurity = new rbwebdesigns\AppSecurity();
    

/****************************************************************
  Setup View
****************************************************************/

    $view = new View($models);


/****************************************************************
  Setup controller
****************************************************************/
    
    // Handle Page Load
    $endpoint = $router->lookup($controllerName);
    if($endpoint === false) $endpoint = $router->lookup("home");
    
    // Create controller
    switch($controllerName)
    {
        case 'posts':
            require_once SERVER_ROOT.'/app/controller/postscms_controller.inc.php';
            $page_controller = new PostsController($cms_db, $view);
            break;

        case 'config':
            require_once SERVER_ROOT.'/app/controller/settings_controller.inc.php';
            $page_controller = new SettingsController($cms_db, $view);
            break;
            
        case 'files':
            require_once SERVER_ROOT.'/app/controller/files_controller.inc.php';
            $page_controller = new FilesController($cms_db, $view);
            break;
        
        case 'contributors':
            require_once SERVER_ROOT.'/app/controller/contributors_controller.inc.php';
            $page_controller = new ContributorController($cms_db, $view);
            break;
        
        case 'account':
            require_once SERVER_ROOT.'/app/controller/account_controller.inc.php';
            $page_controller = new AccountController($cms_db, $view);
            break;
        
        case 'ajax':
            require_once SERVER_ROOT.'/app/controller/ajax_controller.inc.php';
            $page_controller = new AjaxController($cms_db, $view, $queryParams);
            exit; // go no further in this script!
            break;
                        
        default:
            require_once SERVER_ROOT.'/app/controller/blogcms_controller.inc.php';
            $page_controller = new MainController($cms_db, $view);
            break;
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
    $view->setSideMenu($page_controller->getSideMenu($queryParams, $controllerName));
    
    // Title, description could also be dynamically assigned in this function call
    $page_controller->$endpoint['f']($queryParams);
    
?>