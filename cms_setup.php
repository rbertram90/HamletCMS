<?php

use Athens\CSRF;
use HamletCMS\HamletCMS;
use HamletCMS\Menu;

/****************************************************************
  CMS Entry point
****************************************************************/

    // Create custom request, shifting the query path along one.
    // First part of query variable is the controller name.
    $queryPath = filter_input(INPUT_GET, 'query');
    $queryParts = explode('/', $queryPath);
    $controllerName = $queryParts[0] ?? '';

    // Set the correct controller.
    $_REQUEST['p'] = $controllerName ?: 'index';

    // Set the correct query.
    if (count($queryParts) > 1) {
        $_REQUEST['query'] = implode('/', array_slice($queryParts, 1));
    }
    else {
        $_REQUEST['query'] = '';
    }

    $request = HamletCMS::request();
    $response = HamletCMS::response();
    
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
            $controller = new \HamletCMS\UserAccounts\controller\UserAccounts();
            $controller->$action();
            exit;
        }
    }

    // User must be logged in to do anything in the CMS
    if (!USER_AUTHENTICATED) {
        $response->redirect('/cms/account/login', 'Login required', 'warning');
    }
    elseif ($controllerName == 'index') { // no such thing as index controller
        $response->redirect('/cms/blog');
    }

    $user = HamletCMS::session()->currentUser;
    $modelPermissions = HamletCMS::model('permissions');
    
    // Check the user has access to view/edit this blog
    $blogID = $request->getUrlParameter(1);
    if (strlen($blogID) == 10 && is_numeric($blogID)) {
        HamletCMS::$blogID = $blogID;

        // Surely must be an ID for a blog
        // Check the user has edit permissions
        HamletCMS::$userGroup = $modelPermissions->getUserGroup($blogID);

        if (!HamletCMS::$userGroup) {
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
    if ($route = HamletCMS::pathMatch()) {
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
    $response->addStylesheet('/hamlet/css/semantic.css');
    $response->addStylesheet('/hamlet/css/blogs_stylesheet.css');
    $response->addStylesheet('/hamlet/css/messages.css');

    // Add default script(s)
    $response->addScript('/hamlet/resources/js/jquery-1.8.0.min.js');
    $response->addScript('/hamlet/js/semantic.js');
    $response->addScript('/hamlet/resources/js/core-functions.js');
    $response->addScript('/hamlet/resources/js/validate.js');
    $response->addScript('/hamlet/resources/js/ajax.js');
    $response->addScript('/hamlet/js/sidemenu.js');

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

    if (! HamletCMS::$hideActionsMenu) {
        $sideMenu = new Menu('cms_main_actions');
        HamletCMS::runHook('onGenerateMenu', ['id' => 'cms_main_actions', 'menu' => &$sideMenu]);
        $response->setVar('page_side_menu', $sideMenu);
    }

    if ($user['admin'] == 1) {
        $adminMenu = new Menu('cms_admin_actions');
        HamletCMS::runHook('onGenerateMenu', ['id' => 'cms_admin_actions', 'menu' => &$adminMenu]);
        $response->setVar('page_admin_menu', $adminMenu);
    }

    $userMenu = new Menu('cms_user_actions');
    HamletCMS::runHook('onGenerateMenu', ['id' => 'cms_user_actions', 'menu' => &$userMenu]);
    $response->setVar('page_user_menu', $userMenu);

    
/****************************************************************
  Run wrapping template
****************************************************************/

    // Run Template here
    $userModel = HamletCMS::model('\\HamletCMS\\UserAccounts\\model\\UserAccounts');
    $response->setVar('user', $userModel->getById($user['id']));

    $blogsModel = HamletCMS::model('\\HamletCMS\\Blog\\model\\Blogs');
    $response->setVar('blogs', $blogsModel->getBlogsByUser($user['id']));

    $response->setVar('hideActionsMenu', HamletCMS::$hideActionsMenu);
    
    $response->writeTemplate('template.tpl');
