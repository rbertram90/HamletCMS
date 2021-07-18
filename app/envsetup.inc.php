<?php

use rbwebdesigns\core\JSONhelper;
use HamletCMS\HamletCMS;

    // Load JSON config file
    // Note: cannot use core function to do this as hasn't been loaded
    // at this stage - chicken and egg situation
    if (!file_exists(__DIR__ . '/config/config.json')) {
        die('Site not configured - please create file /app/config/config.json');
    }

    $config = JSONhelper::JSONFileToArray(__DIR__ . '/config/config.json');

    define('IS_DEVELOPMENT', $config['environment']['development_mode']); // Flag for development
    
    if (!defined('SERVER_ROOT')) {
        define('SERVER_ROOT', $config['environment']['root_directory']);  // Absolute path to root folder
    }
    define('SERVER_CMS_ROOT', SERVER_ROOT . '/app/cms');
    define('SERVER_PUBLIC_PATH', SERVER_ROOT . '/public');           // Path to public document root
    define('SERVER_MODULES_PATH', SERVER_ROOT . '/app/modules');     // Path to modules
    define('SERVER_PATH_TEMPLATES', SERVER_ROOT . '/app/templates'); // Path to the blog templates
    define('SERVER_PATH_BLOGS', SERVER_PUBLIC_PATH . '/blogdata');   // Path to public blog data
    define('SERVER_AVATAR_FOLDER', SERVER_PUBLIC_PATH . '/avatars'); // Path to the folder containing user avatars
    define('SERVER_PATH_WIDGETS', SERVER_ROOT . '/app/widgets');     // Path to installed widgets

    // Make sure we're in the right timezone
    date_default_timezone_set($config['environment']['timezone']);


/****************************************************************
  Session Handling
****************************************************************/
    
    // Start Session if not already started
    if (!isset($_SESSION)) session_start();
    
    if (IS_DEVELOPMENT) {
        error_reporting(E_STRICT && E_ALL);
        ini_set('display_errors', 1);
    }
    else {
        error_reporting(0);
        ini_set('display_errors', 0);
    }

/****************************************************************
  Includes
****************************************************************/
/*
    spl_autoload_register(function ($class) {
        $split = explode('\\', $class);
        if (count($split) < 3) error_log('Unable to load class '. $class);
        $type = strtolower($split[2]);
        $subDirectory = file_exists(SERVER_MODULES_PATH ."/core/{$split[1]}") ? 'core' : 'addon';
        if (in_array($type, ['controller', 'model', 'widgets', 'forms', 'tests'])) {
            include SERVER_MODULES_PATH ."/{$subDirectory}/{$split[1]}/src/{$type}/{$split[3]}.php";
        }
        else {
            include SERVER_MODULES_PATH ."/{$subDirectory}/{$split[1]}/src/{$split[2]}.php";
        }
    });
*/
    // Include core classes
    // require_once SERVER_ROOT .'/app/response.php';
    // require_once SERVER_ROOT .'/app/menu.php';
    // require_once SERVER_ROOT .'/app/menulink.php';
    // require_once SERVER_ROOT .'/app/module.php';
    // require_once SERVER_ROOT .'/app/cms.php';
    // require_once SERVER_ROOT .'/app/abstractcontroller.php';

    // Smarty
    require_once SERVER_ROOT .'/app/vendor/smarty/smarty/libs/Smarty.class.php';
        
    // Import view functions
    require_once SERVER_ROOT .'/app/view/helper_functions.php';

    // Store the configuration
    HamletCMS::addToConfig($config);

// Continue if not installing
if ($_SERVER['SCRIPT_NAME'] != '/cms/install.php') {


/****************************************************************
  Database Constants
****************************************************************/
    
    if(!array_key_exists('database', $config)) die("Setup error - no database config found");
    $databaseCredentials = $config['database'];

    $dbc = HamletCMS::databaseConnection();
    $checkInstall = $dbc->countRows("information_schema.tables", [
        'table_schema' => $databaseCredentials['name'],
        'table_name' => 'modules'
    ]);

    // Didn't find any modules - run install!
    if ($checkInstall == 0) {
        HamletCMS::response()->redirect('/cms/install.php');
    }

    define("TBL_BLOGS", $databaseCredentials['name'] . ".blogs");
    define("TBL_POSTS", $databaseCredentials['name'] . ".posts");
    define("TBL_POST_VIEWS", $databaseCredentials['name'] . ".postviews");
    define("TBL_AUTOSAVES", $databaseCredentials['name'] . ".postautosaves");
    define("TBL_CONTRIBUTORS", $databaseCredentials['name'] . ".contributors");
    define("TBL_USERS", $databaseCredentials['name'] . ".users");
    

/****************************************************************
  Set-Up Hooks
****************************************************************/    

    // Import all modules
    // $directoryListing = new \DirectoryIterator(SERVER_ROOT . '/app/modules');
    $moduleModel = HamletCMS::model('\\HamletCMS\\SiteAdmin\\model\\Modules');
    $modules = $moduleModel->getList();

    foreach ($modules as $module) {
        if ($module->enabled != 1) continue;
        HamletCMS::registerModule($module->name);
    }

}