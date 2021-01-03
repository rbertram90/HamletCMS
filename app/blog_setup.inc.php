<?php

namespace rbwebdesigns\HamletCMS;

use rbwebdesigns\core\Sanitize;
use rbwebdesigns\HamletCMS\BlogView\controller\BlogContent;
use Athens\CSRF;

/**
 * blog_setup.inc.php
 * set-up code when viewing blogs
 * 
 * @author Ricky Bertram
 * @date MAR 2013
 *  
 * Session Handling
 * Core System Includes
 * Database Connection
 * Global variables
 * Global JavaScript and CSS
 */

// Setup for 'Plugins' Installed using composer
if (!class_exists("\Composer\Autoload\ClassLoader")) {
  require_once SERVER_ROOT . '/app/vendor/autoload.php';
}

// Setup - Stage 1
// May have already been required in setup.inc.php if we're not using custom
// domain. Otherwise will be included here
require_once SERVER_ROOT .'/app/envsetup.inc.php';

// Intialize CSRF tokens
CSRF::init();


/****************************************************************
    Setup and route page content
****************************************************************/

    if (!defined('BLOG_KEY')) {
        $parts = explode('/', $_SERVER['DOCUMENT_ROOT']);
        define('BLOG_KEY', array_pop($parts));
    }

    // Feed the blog ID to CMS class
    HamletCMS::$blogID = BLOG_KEY;

    HamletCMS::$function = 'blogview'; // changing this variable to something other than 'cms' as otherwise custom domain routing does not work. @todo something cleaner

    $modelPermissions = HamletCMS::model('rbwebdesigns\HamletCMS\Contributors\model\Permissions');
    HamletCMS::$userGroup = $modelPermissions->getUserGroup(BLOG_KEY);

    $request = HamletCMS::request();
    $response = HamletCMS::response();
    $blog = HamletCMS::getActiveBlog();
    $config = HamletCMS::config();
    $page_controller = new BlogContent(BLOG_KEY);
    
    if (CUSTOM_DOMAIN) {
        $host = $config['environment']['canonical_domain'];
        $action = $request->getControllerName();
        $pathPrefix = $blogDir = '';
    }
    else {
        $host = '';
        $action = strtolower($request->getUrlParameter(1));
        $pathPrefix = "/blogs/{$blog->id}";
        $blogDir = "/blogdata/{$blog->id}";
    }

    $session = HamletCMS::session();

    // Check if we are logged in
    if (gettype($session->currentUser) == 'array') {
        define('USER_AUTHENTICATED', true);
    }
    else {
        define('USER_AUTHENTICATED', false);
    }

    $response->addScript($host .'/resources/js/jquery-1.8.0.min.js');
    $response->addScript($host .'/resources/js/core-functions.js');
    $response->addScript($host .'/resources/js/validate.js');
    $response->addScript($host .'/resources/js/galleria-1.4.2.min.js');
    $response->addScript($host .'/resources/js/galleria.classic.min.js');
    
    $response->setVar('cms_url', $host);
    $response->setVar('blog', $blog);
    $response->setVar('blog_key', BLOG_KEY);
    $response->setVar('blog_root_url', $pathPrefix);
    $response->setVar('blog_file_dir', $blogDir);
    $response->setVar('custom_domain', CUSTOM_DOMAIN);
    $response->setTitle('Default Page Title');
    $response->setDescription('Default Page Description');

    $widgetsController = new \rbwebdesigns\HamletCMS\Widgets\controller\WidgetsView();
    $response->setVar('widgets', $widgetsController->generatePlaceholders());
    
    $response->setVar('user_is_contributor', HamletCMS::$userGroup !== false);
    $response->setVar('user_is_logged_in', USER_AUTHENTICATED);
    $response->setVar('page_headerbackground', $page_controller->generateHeaderBackground());
    $response->setVar('header_content', $page_controller->generateHeader());
    $response->setVar('footer_content', $page_controller->generateFooter());

    // Get side menu items
    // @todo Pass current post through
    $sideMenu = new Menu('blog_actions');
    HamletCMS::runHook('onGenerateMenu', [
      'id' => 'blog_actions',
      'menu' => &$sideMenu,
      'blog' => $blog
    ]);
    $response->setVar('blog_actions_menu', $sideMenu);

    $templateConfig = $page_controller->getTemplateConfig();
    $response->setVar('template_config', $templateConfig);

    if (isset($templateConfig['Includes'])) {
        $includes = $templateConfig['Includes'];
        if (isset($includes['semantic-ui']) && $includes['semantic-ui']) {
            $response->addStylesheet($host . '/css/semantic.css');
            $response->addScript($host . '/js/semantic.js');
        }
    }
    if (isset($templateConfig['Imports'])) {
        foreach ($templateConfig['Imports'] as $file) {
            $response->addStylesheet($file);
        }
    }

    // Store any output in a buffer
    ob_start();

    if ($route = HamletCMS::pathMatch()) {
        // New dynamic routes
        $contentController = new $route['controller']();
        $contentController->{$route['action']}();
    }
    else {
        // Older static routes - @todo change to dynamic routing
        switch ($action) {
            case "posts":
                $page_controller->viewPost();
                break;
                
            case "tags":
                // Search for posts with tag
                $page_controller->viewPostsByTag();
                break;

            case 'author':
                $page_controller->viewPostsByAuthor();
                break;
                
            default:
                // View Homepage
                $page_controller->viewHome();
                break;
        }
    }

    $response->setBody(ob_get_contents());

    ob_end_clean();

    $response->writeTemplate('main.tpl', 'BlogView');

