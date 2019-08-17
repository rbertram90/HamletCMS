<?php
namespace rbwebdesigns\blogcms;

use Codeliner;
use Athens\CSRF;
use rbwebdesigns\core\Sanitize;

/****************************************************************
  CMS Entry point
****************************************************************/

    // Include cms setup script
    require_once __DIR__ . '/../../app/setup.inc.php';

    
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
    
    if ($controllerName == 'account') {
        $action = $request->getUrlParameter(0, 'login');

        if ($action == 'login' || $action == 'register' || $action == 'resetpassword') {
            $controller = new \rbwebdesigns\blogcms\UserAccounts\controller\UserAccounts();
            $controller->$action();
            exit;
        }
    }

    // User must be logged in to do anything in the CMS
    if (!USER_AUTHENTICATED) {
        $response->redirect('/cms/account/login', 'Login required', 'warning');
    }

    $user = BlogCMS::session()->currentUser;
    $modelPermissions = BlogCMS::model('\rbwebdesigns\blogcms\Contributors\model\Permissions');
    
    // Check the user has access to view/edit this blog
    $blogID = $request->getUrlParameter(1);
    if (strlen($blogID) == 10 && is_numeric($blogID)) {
        BlogCMS::$blogID = $blogID;

        // Surely must be an ID for a blog
        // Check the user has edit permissions
        BlogCMS::$userGroup = $modelPermissions->getUserGroup($blogID);

        if (!BlogCMS::$userGroup) {
            $response->redirect('/', 'You\'re not a contributor for that blog!', 'error');
        }
    }

    // Check form submissions for CSRF token
    CSRF::init();


/****************************************************************
  Setup controller
****************************************************************/
    
    // New - get the controller from pre-defined routes in modules
    $found = false;
    if ($route = BlogCMS::pathMatch()) {
        if ($route['controller'] && $route['action']) {
            $found = true;
            $controller = new $route['controller']();
            $action = $route['action'];
        }
    }

    if (!$found) {
        $response->redirect('/cms', 'Page not found', 'error');
    }


/****************************************************************
  Get body content
****************************************************************/

    // Add default stylesheet(s)
    $response->addStylesheet('/css/semantic.css');
    // $this->addStylesheet('/resources/css/core');
    $response->addStylesheet('/resources/css/header.css');
    // $this->addStylesheet('/resources/css/forms');
    $response->addStylesheet('/css/blogs_stylesheet.css');
    $response->addStylesheet('/css/messages.css');

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
  Generate side menu
****************************************************************/

    $sideMenu = new Menu('cms_main_actions');
    BlogCMS::runHook('onGenerateMenu', ['id' => 'cms_main_actions', 'menu' => &$sideMenu]);

    if ($user['admin'] == 1) {
        $adminMenu = new Menu('cms_admin_actions');
        BlogCMS::runHook('onGenerateMenu', ['id' => 'cms_admin_actions', 'menu' => &$adminMenu]);
        $response->setVar('page_admin_menu', $adminMenu);
    }

    $userMenu = new Menu('cms_user_actions');
    BlogCMS::runHook('onGenerateMenu', ['id' => 'cms_user_actions', 'menu' => &$userMenu]);

    $response->setVar('page_side_menu', $sideMenu);
    $response->setVar('page_user_menu', $userMenu);

    
/****************************************************************
  Run wrapping template
****************************************************************/

    // Run Template here
    $userModel = BlogCMS::model('\\rbwebdesigns\\blogcms\\UserAccounts\\model\\UserAccounts');
    $response->setVar('user', $userModel->getById($user['id']));

    $blogsModel = BlogCMS::model('\\rbwebdesigns\\blogcms\\Blog\\model\\Blogs');
    $response->setVar('blogs', $blogsModel->getBlogsByUser($user['id']));
    
    $response->writeTemplate('template.tpl');
