<?php
namespace rbwebdesigns\blogcms;

use rbwebdesigns\core\Database;
use rbwebdesigns\core\JSONhelper;

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
    define('SERVER_PUBLIC_PATH', SERVER_ROOT . '/public');        // Path to www folder
    define('SERVER_MODULES_PATH', SERVER_ROOT . '/app/modules');      // Path to modules folder
    define('SERVER_PATH_TEMPLATES', SERVER_ROOT . '/templates');      // Path to the blog templates folder
    define('SERVER_PATH_BLOGS', SERVER_PUBLIC_PATH . '/blogdata');    // Path to the blogs data
    define('SERVER_AVATAR_FOLDER', SERVER_PUBLIC_PATH . '/avatars');  // Path to the folder containing user avatars
    define('SERVER_PATH_WIDGETS', SERVER_ROOT . '/app/widgets');      // Path to installed widgets

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

    spl_autoload_register(function ($class) {
        $split = explode('\\', $class);
        if (count($split) < 4) error_log('Unable to load class '. $class);
        $type = strtolower($split[3]);
        if ($type == 'controller' || $type == 'model') {
            include SERVER_MODULES_PATH ."/{$split[2]}/src/{$type}/{$split[4]}.php";
        }
        else {
            include SERVER_MODULES_PATH ."/{$split[2]}/src/{$split[3]}.php";
        }
    });

    // Include core classes
    require_once SERVER_ROOT .'/app/response.php';
    require_once SERVER_ROOT .'/app/menu.php';
    require_once SERVER_ROOT .'/app/menulink.php';
    require_once SERVER_ROOT .'/app/module.php';
    require_once SERVER_ROOT .'/app/cms.php';
    require_once SERVER_ROOT .'/app/abstractcontroller.php';

    // Smarty
    require_once SERVER_ROOT .'/app/vendor/smarty/smarty/libs/Smarty.class.php';
        
    // Import view functions
    require_once SERVER_ROOT .'/app/view/page_header.php';

    // Store the configuration
    BlogCMS::addToConfig($config);

// Continue if not installing
if ($_SERVER['SCRIPT_NAME'] != '/cms/install.php') {


/****************************************************************
  Database Constants
****************************************************************/
    
    if(!array_key_exists('database', $config)) die("Setup error - no database config found");
    $databaseCredentials = $config['database'];

    $dbc = BlogCMS::databaseConnection();
    $checkInstall = $dbc->countRows("information_schema.tables", [
        'table_schema' => $databaseCredentials['name'],
        'table_name' => 'modules'
    ]);

    // Didn't find any modules - run install!
    if ($checkInstall == 0) {
        BlogCMS::response()->redirect('/cms/install.php');
    }

    define("TBL_BLOGS", $databaseCredentials['name'] . ".blogs");
    define("TBL_POSTS", $databaseCredentials['name'] . ".posts");
    define("TBL_POST_VIEWS", $databaseCredentials['name'] . ".postviews");
    define("TBL_AUTOSAVES", $databaseCredentials['name'] . ".postautosaves");
    define("TBL_COMMENTS", $databaseCredentials['name'] . ".comments"); // @todo remove
    define("TBL_CONTRIBUTORS", $databaseCredentials['name'] . ".contributors");
    define("TBL_FAVOURITES", $databaseCredentials['name'] . ".favourites");
    define("TBL_USERS", $databaseCredentials['name'] . ".users");
    

/****************************************************************
  Set-Up Hooks
****************************************************************/    

    // Import all modules
    // $directoryListing = new \DirectoryIterator(SERVER_ROOT . '/app/modules');
    $moduleModel = BlogCMS::model('\rbwebdesigns\blogcms\SiteAdmin\model\Modules');
    $modules = $moduleModel->getList();

    foreach ($modules as $module) {
        if ($module->enabled != 1) continue;
        BlogCMS::registerModule($module->name);
    }

}