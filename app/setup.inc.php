<?php
namespace rbwebdesigns\blogcms;

use rbwebdesigns\core\Router;
use rbwebdesigns\core\JSONhelper;
use rbwebdesigns\core\model\UserFactory;

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
    require_once __DIR__ . '/vendor/autoload.php';

    // Setup common between cms and blog front-end
    require_once __DIR__ . '/envsetup.inc.php';


/****************************************************************
  Set-Up Users Model & Auth flags
****************************************************************/    
    
    // Connect to users model
    $modelUsers = new UserFactory($cms_db);
  
    $session = BlogCMS::session();

    // Check if we are logged in
    if(gettype($session->currentUser) == 'array') {
        define('USER_AUTHENTICATED', true);
    }
    else {
        define('USER_AUTHENTICATED', false);
    }
    