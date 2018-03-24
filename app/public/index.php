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

    if ($controllerName == 'account') {
        $action = $request->getUrlParameter(0, 'login');

        if ($action == 'login' || $action == 'register') {
            require SERVER_ROOT . '/app/controller/account_controller.inc.php';
            $controller = new \rbwebdesigns\blogcms\AccountController();
            $controller->$action($request, $response);
            exit;
        }
    }

    // User must be logged in to do anything in the CMS
    if (!USER_AUTHENTICATED) {
        $response->redirect('/account/login', 'Login required', 'warning');
    }
    else {
        // Check the user has access to view/edit this blog
        $blogID = $request->getUrlParameter(1);
        if(strlen($blogID) == 10 && is_numeric($blogID)) {
            BlogCMS::$blogID = $blogID;

            // Surely must be an ID for a blog
            // Check the user has edit permissions
            $user = BlogCMS::session()->currentUser;
            $modelContributors = BlogCMS::model('\rbwebdesigns\blogcms\model\Contributors');

            BlogCMS::$userIsContributor = $modelContributors->isBlogContributor($blogID, $user['id']);
            BlogCMS::$userIsAdminContributor = $modelContributors->isBlogContributor($blogID, $user['id'], 'all');

            if (!BlogCMS::$userIsContributor) {
                redirect('/', 'You\'re not a contributor for that blog!', 'error');
            }
            elseif ($controllerName == 'settings') {
                if(!BlogCMS::$userIsAdminContributor) {
                    redirect('/', 'You haven\'t got sufficient permissions to access that page', 'error');
                }
            }
        }
    }

    // Check form submissions for CSRF token
    CSRF::init();


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

/****************************************************************
  Get body content
****************************************************************/

    // Add default stylesheet(s)
    $response->addStylesheet('/css/semantic.css');
    // $this->addStylesheet('/resources/css/core');
    $response->addStylesheet('/resources/css/header.css');
    // $this->addStylesheet('/resources/css/forms');
    $response->addStylesheet('/css/blogs_stylesheet.css');

    // Add default script(s)
    $response->addScript('/resources/js/jquery-1.8.0.min.js');
    $response->addScript('/js/semantic.js');
    $response->addScript('/resources/js/core-functions.js');
    $response->addScript('/resources/js/validate.js');
    $response->addScript('/resources/js/ajax.js');
    $response->addScript('/js/sidemenu.js');

    $response->setTitle('Default title');
    $response->setDescription('Default page description');

    // Call the requested function
    $action = $request->getUrlParameter(0, 'defaultAction');
    ob_start();
    $controller->$action($request, $response);
    $response->setBody(ob_get_contents());
    ob_end_clean();

    // Cases where template not required
    if ($controllerName == 'ajax' || $controllerName == 'api' || $request->isAjax) {
        $response->writeBody();
        exit;
    }

    
/****************************************************************
  Output Template
****************************************************************/

    // Set the side menu content
    // $view->setSideMenu($controller->getSideMenu($queryParams, $action));

    require SERVER_ROOT . '/app/view/sidemenu.php';

    if (BlogCMS::$blogID) {
        $sideMenu = getCMSSideMenu(BlogCMS::$blogID, BlogCMS::$userIsAdminContributor, BlogCMS::$activeMenuLink);
    }
    else {
        $sideMenu = getCMSSideMenu(0, 0, BlogCMS::$activeMenuLink);
    }
    $response->setVar('page_sidemenu', $sideMenu);

    // Run Template here
    $response->writeTemplate('template.tpl');