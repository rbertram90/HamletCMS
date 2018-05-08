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

    // Load JSON config file
    // Note: cannot use core function to do this as hasn't been loaded
    // at this stage - chicken and egg situation
    $config = JSONhelper::JSONFileToArray(__DIR__ . '/config/config.json');

    define('IS_DEVELOPMENT', $config['environment']['development_mode']); // Flag for development
    
    define('SERVER_ROOT', $config['environment']['root_directory']);  // Absolute path to root folder
    define('SERVER_CMS_ROOT', SERVER_ROOT . '/app/cms');
    define('SERVER_PUBLIC_PATH', SERVER_ROOT . '/app/public');        // Path to www folder
    define('SERVER_PATH_TEMPLATES', SERVER_ROOT . '/templates');      // Path to the blog templates folder
    define('SERVER_PATH_BLOGS', SERVER_PUBLIC_PATH . '/blogdata');    // Path to the blogs data
    define('SERVER_AVATAR_FOLDER', SERVER_PUBLIC_PATH . '/avatars');  // Path to the folder containing user avatars
    define('SERVER_PATH_WIDGETS', SERVER_ROOT . '/app/widgets');      // Path to installed widgets

    // Make sure we're in the right timezone
    date_default_timezone_set($config['environment']['timezone']);

    // Setup common between cms and blog front-end
    require_once SERVER_ROOT . '/app/envsetup.inc.php';

    // Store the configuration
    BlogCMS::addToConfig($config);

/****************************************************************
  Set-Up Hooks
****************************************************************/    

    // Import all addons
    $directoryListing = new \DirectoryIterator(SERVER_ROOT . '/app/addons');

    foreach ($directoryListing as $file) {
        if ($file->getExtension() == 'php') {
            require_once SERVER_ROOT . '/app/addons/' . $file->getFilename();

            $className = $file->getBasename('.php');
            BlogCMS::registerAddon($className);
        }
    }

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
    