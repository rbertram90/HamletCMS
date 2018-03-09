<?php
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

    namespace rbwebdesigns\blogcms;
    use rbwebdesigns;

    // Setup Directory and Core DB Constants
    // require_once dirname(__FILE__).'/../../root.inc.php';
	
    // Setup - Stage 1
    require_once SERVER_ROOT.'/app/envsetup.inc.php';
	
    // Setup for 'Plugins' Installed using composer
    require_once SERVER_ROOT.'/app/vendor/autoload.php';
	
    // Include blogs controller
    require_once SERVER_ROOT.'/app/controller/blogcontent_controller.inc.php';
    
    
/****************************************************************
  Set-Up Users Model & Auth flags
****************************************************************/    
    
    // Connect to Users Database
    $modelUsers = new rbwebdesigns\Users($cms_db);
	

/****************************************************************
  Stylesheet
****************************************************************/

    $global_css_includes = array(
        '/resources/css/core',
        '/resources/css/header',
        '/resources/css/forms'
    );

    // blog_cms Specific CSS
    $css_includes = array();


/****************************************************************
  JavaScript
****************************************************************/
    
    $global_js_includes = array(
        '/resources/js/jquery-1.8.0.min',
        '/resources/js/core-functions',
        '/resources/js/validate',
        '/resources/js/galleria-1.4.2.min',
        '/resources/js/galleria.classic.min'
    );
    // blog_cms Specific JS
    $js_includes = array(
        '/projects/blog_cms/js/addFavourite'
    );


/****************************************************************
    Setup and Decide on actual page content
****************************************************************/

    // Have now got key from public/index.php

    // $blog_key = substr(dirname($_SERVER["PHP_SELF"]), -10);

    // if(strlen($blog_key) !== 10 || !is_numeric($blog_key)) {
    //     // try again - this is used by opencoding.co.uk
    //     $blog_key = substr(dirname($_SERVER['SCRIPT_FILENAME']), -10);
    // }

    // Create Controller
    $page_controller = new BlogContentController($cms_db, BLOG_KEY);
    $gobjBlog = $page_controller->getBlogInfo();
    
    // Get style defined using the colour pickers
    $custom_css = $page_controller->getBlogCustomCSS(BLOG_KEY);

    // Data Required page - Defaulted
    $DATA = array(
        'page_title' => 'Default Page Title',
        'page_description' => 'Default Page Description',
        'includes_css' => array_merge($global_css_includes, $css_includes),
        'includes_js' => array_merge($global_js_includes, $js_includes),
        'page_content' => '',
        'widget_content' => $page_controller->generateWidgets(),
        'blog_key' => BLOG_KEY,
        'custom_css' => $custom_css,
        'user_is_contributor' => $page_controller->userIsContributor(),
        'is_favourite' => $page_controller->blogIsFavourite(),
        'page_headerbackground' => $page_controller->generateHeaderBackground(),
        'page_footercontent' => $page_controller->generateFooter(),
		'page_navigation' => $page_controller->generateNavigation(),
        'header_hide_title' => $page_controller->header_hideTitle,
        'header_hide_description' => $page_controller->header_hideDescription,
		'template_config' => $page_controller->getTemplateConfig()
    );
    
	// Proccess Query String - note has probabily already been done at index level!
    if(isset($queryParams))
    {
        $p = array_shift($queryParams); // get and remove the first element of array
    }
    else
    {
        if(isset($_GET['query']))
        {
            $queryParams = strlen($_GET['query']) > 0 ? explode("/", $_GET['query']) : false;
        }
        else
        {
            $queryParams = "";
        }
        
        // Name of article
        $p = isset($_GET['p']) ? safeString($_GET['p']) : -1;
    }
    

	// Store any output in a buffer
	ob_start();

	switch(strtolower($p)):

		case "posts":

			if(gettype($queryParams) !== "array")
            {
				$DATA = $page_controller->viewHome($DATA, $queryParams);
				break;
			}
			
			if(array_key_exists(1, $queryParams))
            {
				// Perform an action based on a post
				$action = safeString($queryParams[1]);
                
				switch($action)
                {
                    case "addcomment":
						// Add a comment to this post
						$DATA = $page_controller->addComment($DATA, $queryParams);
						break;
                        
					default:
						// View the post
						$DATA = $page_controller->viewPost($DATA, $queryParams);
                        break;
                }
			}
            else
            {
				// View Post
				$DATA = $page_controller->viewPost($DATA, $queryParams);
			}
			break;
			
		case "tags":
			// Search for posts with tag
			$DATA = $page_controller->viewPostsByTag($DATA, $queryParams);
			break;
			
        case "search":
			// Search for posts with tag
			$DATA = $page_controller->search($DATA, $queryParams);
			break;

		default:
			// View Homepage
			$DATA = $page_controller->viewHome($DATA, $queryParams);
			break;

	endswitch;

	$DATA['page_content'] = ob_get_contents();

	ob_end_clean();
	
	require SERVER_ROOT.'/app/blog_template.php';
?>