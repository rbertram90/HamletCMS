<?php
namespace rbwebdesigns\blogcms;

use rbwebdesigns\core\Sanitize;

/***************************************************************
    blog_setup.inc.php
    @description set-up code for blogs within the cms, need
    seperate file as the include paths are different.
    @author R Bertram
    @date MAR 2013
    
     * Session Handling
     * Core System Includes
     * Database Connection
     * Global variables
     * Global JavaScript and CSS
****************************************************************/
    
    // Setup - Stage 1
    require_once SERVER_ROOT.'/app/envsetup.inc.php';
    
    // Setup for 'Plugins' Installed using composer
    require_once SERVER_ROOT.'/app/vendor/autoload.php';
    
    // Include blogs controller
    require_once SERVER_ROOT.'/app/controller/blogcontent_controller.inc.php';


/****************************************************************
    Setup and Decide on actual page content
****************************************************************/

    $request = BlogCMS::request();
    $response = BlogCMS::response();

    $page_controller = new BlogContentController(BLOG_KEY);
    $blog = $page_controller->getBlogInfo();

    $response->addStylesheet('/resources/css/core.css');

    $response->addScript('/resources/js/jquery-1.8.0.min.js');
    $response->addScript('/resources/js/core-functions.js');
    $response->addScript('/resources/js/validate.js');
    $response->addScript('/resources/js/galleria-1.4.2.min.js');
    $response->addScript('/resources/js/galleria.classic.min.js');
    $response->addScript('/projects/blog_cms/js/addFavourite.js');
    
    $response->setVar('blog', $blog);
    $response->setVar('blog_key', BLOG_KEY);
    $response->setTitle('Default Page Title');
    $response->setDescription('Default Page Description');
    $response->setVar('custom_css', $page_controller->getBlogCustomCSS(BLOG_KEY));

    $response->setVar('widget_content', $page_controller->generateWidgets());
    $response->setVar('user_is_contributor', $page_controller->userIsContributor()); // @todo this is broken...
    $response->setVar('user_is_logged_in', USER_AUTHENTICATED);
    $response->setVar('is_favourite', $page_controller->blogIsFavourite());
    $response->setVar('page_headerbackground', $page_controller->generateHeaderBackground());
    $response->setVar('page_footercontent', $page_controller->generateFooter());
    $response->setVar('page_navigation', $page_controller->generateNavigation());
    $response->setVar('header_hide_title', $page_controller->header_hideTitle);
    $response->setVar('header_hide_description', $page_controller->header_hideDescription);

    $templateConfig = $page_controller->getTemplateConfig();
    $response->setVar('template_config', $templateConfig);

    if (isset($templateConfig['Includes'])) {
        $includes = $templateConfig['Includes'];
        if (isset($includes['semantic-ui']) && $includes['semantic-ui']) {
            $response->addStylesheet('/css/semantic.css');
            $response->addScript('/js/semantic.js');
        }
    }

    // Store any output in a buffer
    ob_start();

    switch (strtolower($request->getUrlParameter(1))) {
        case "posts":
            $page_controller->viewPost($request, $response);
            break;
            
        case "addcomment":
            // Add a comment to this post
            $page_controller->addComment($request, $response);
            break;
            
        case "tags":
            // Search for posts with tag
            $page_controller->viewPostsByTag($request, $response);
            break;
            
        case "search":
            // Search for posts with tag
            $page_controller->search($request, $response);
            break;

        default:
            // View Homepage
            $page_controller->viewHome($request, $response);
            break;
    }

    $response->setBody(ob_get_contents());

    ob_end_clean();

    $response->writeTemplate('blog/main.tpl');
