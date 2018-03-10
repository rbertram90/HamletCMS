<?php
namespace rbwebdesigns\blogcms;
use rbwebdesigns;
use rbwebdesigns\core;

/**
 * app/setup.php
 * 
 * Required at the top of the cms index page, after the root include
 * but before anything is printed out. For the front-end blogs see
 * blog_setup.php
 * 
 * @author R Bertram <ricky@rbwebdesigns.co.uk>
 */

// Composer setup
require_once SERVER_ROOT . '/app/vendor/autoload.php';

require_once SERVER_ROOT . '/app/cms.php';

// Setup common between cms and blog front-end
require_once SERVER_ROOT . '/app/envsetup.inc.php';


/****************************************************************
  Router
****************************************************************/

    $pagelist = rbwebdesigns\core\JSONHelper::JSONFileToArray(SERVER_ROOT . '/app/config/routes.json');
    $router = new rbwebdesigns\core\Router($pagelist);


/****************************************************************
  Set-Up Users Model & Auth flags
****************************************************************/    
    
    // Connect to users model
    $modelUsers = new rbwebdesigns\core\model\UserFactory($cms_db);
  
    $session = BlogCMS::session();

    // Check if we are logged in
    if($session->currentUser) {
        define('USER_AUTHENTICATED', true);
    }
    else {
        define('USER_AUTHENTICATED', false);
    }
    
        
/****************************************************************
  Stylesheets - not used?
****************************************************************/

    $global_css_includes = array(
        '/resources/css/core',
        '/resources/css/forms',
    );

/****************************************************************
  JavaScript - not used?
****************************************************************/

    $global_js_includes = array(
        '/resources/js/jquery-1.8.0.min',
        '/resources/js/core-functions',
        '/resources/js/validate',
        '/resources/js/ajax'
    );
