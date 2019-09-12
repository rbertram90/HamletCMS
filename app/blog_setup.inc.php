<?php

namespace rbwebdesigns\blogcms;

use rbwebdesigns\core\Sanitize;
use rbwebdesigns\blogcms\BlogView\controller\BlogContent;
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
require_once SERVER_ROOT .'/app/vendor/autoload.php';

// Setup - Stage 1
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
    BlogCMS::$blogID = BLOG_KEY;

    $modelPermissions = BlogCMS::model('rbwebdesigns\blogcms\Contributors\model\Permissions');
    BlogCMS::$userGroup = $modelPermissions->getUserGroup(BLOG_KEY);

    $request = BlogCMS::request();
    $response = BlogCMS::response();
    $blog = BlogCMS::getActiveBlog();
    $config = BlogCMS::config();
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

    $session = BlogCMS::session();

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
    $response->addScript($host .'/js/addFavourite.js');
    
    $response->setVar('cms_url', $host);
    $response->setVar('blog', $blog);
    $response->setVar('blog_key', BLOG_KEY);
    $response->setVar('blog_root_url', $pathPrefix);
    $response->setVar('blog_file_dir', $blogDir);
    $response->setVar('custom_domain', CUSTOM_DOMAIN);
    $response->setTitle('Default Page Title');
    $response->setDescription('Default Page Description');

    $widgetsController = new \rbwebdesigns\blogcms\Widgets\controller\WidgetsView();
    $response->setVar('widgets', $widgetsController->generatePlaceholders());
    
    $response->setVar('user_is_contributor', BlogCMS::$userGroup !== false);
    $response->setVar('user_is_logged_in', USER_AUTHENTICATED);
    $response->setVar('page_headerbackground', $page_controller->generateHeaderBackground());
    $response->setVar('header_content', $page_controller->generateHeader());
    $response->setVar('footer_content', $page_controller->generateFooter());

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
    
    if ($route = BlogCMS::pathMatch()) {
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
                
            default:
                // View Homepage
                $page_controller->viewHome();
                break;
        }
    }

    $response->setBody(ob_get_contents());

    ob_end_clean();

    $response->writeTemplate('main.tpl', 'BlogView');

