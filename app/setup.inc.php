<?php
namespace rbwebdesigns\blogcms;
use rbwebdesigns;

/****************************************************************
    setup.php
		
    Required at the TOP of the index page, after the root include
    but before anything is echoed out.
    
    @author R Bertram
    @date JAN 2013
    
****************************************************************/

    // Setup for 'Plugins' Installed using composer
    require_once SERVER_ROOT . '/app/vendor/autoload.php';
    
    // Setup - Stage 1
    require_once SERVER_ROOT . '/app/envsetup.inc.php';

	
/****************************************************************
  Router
****************************************************************/

    $pagelist = json_decode(file_get_contents(SERVER_ROOT . '/app/config/sitemap.json'), true);
    $router = new rbwebdesigns\Router($pagelist);


/****************************************************************
  Set-Up Users Model & Auth flags
****************************************************************/    
    
    // Connect to users model
    $modelUsers = new rbwebdesigns\Users($cms_db);
	
    // Check if we are logged in
    if(!isset($_SESSION['userid']))
    {
        define('USER_AUTHENTICATED', false);
    }
    else
    {
        define('USER_AUTHENTICATED', true);
        define('USER_ID', $_SESSION['userid']);
        $gobjUser = $modelUsers->getUserById($_SESSION['userid']);
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

/***************************************************************/
?>