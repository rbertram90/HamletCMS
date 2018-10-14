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
    
    define('SERVER_ROOT', $config['environment']['root_directory']);  // Absolute path to root folder
    define('SERVER_CMS_ROOT', SERVER_ROOT . '/app/cms');
    define('SERVER_PUBLIC_PATH', SERVER_ROOT . '/app/public');        // Path to www folder
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

/****************************************************************
  Database Constants
****************************************************************/
    
    if(!array_key_exists('database', $config)) die("Setup error - no database config found");
    $databaseCredentials = $config['database'];

    define("TBL_BLOGS", $databaseCredentials['name'] . ".blogs");
    define("TBL_POSTS", $databaseCredentials['name'] . ".posts");
    define("TBL_POST_VIEWS", $databaseCredentials['name'] . ".postviews");
    define("TBL_AUTOSAVES", $databaseCredentials['name'] . ".postautosaves");
    define("TBL_COMMENTS", $databaseCredentials['name'] . ".comments");
    define("TBL_CONTRIBUTORS", $databaseCredentials['name'] . ".contributors");
    define("TBL_FAVOURITES", $databaseCredentials['name'] . ".favourites");
    define("TBL_USERS", $databaseCredentials['name'] . ".users");
    
    
/****************************************************************
  Database Connection
****************************************************************/
    
    // Connect to the blog_cms database
    $cms_db = new Database();
    $cms_db->connect($databaseCredentials['server'], $databaseCredentials['name'], $databaseCredentials['user'], $databaseCredentials['password']);
    

/****************************************************************
  Includes
****************************************************************/

    spl_autoload_register(function ($class) {
        $split = explode('\\', $class);
        if (count($split) < 5) error_log('Unable to load class '. $class);
        $type = strtolower($split[3]);
        include SERVER_MODULES_PATH ."/{$split[2]}/src/{$type}/{$split[4]}.php";
    });

    require_once SERVER_ROOT .'/app/response.php';
    require_once SERVER_ROOT .'/app/menu.php';
    require_once SERVER_ROOT .'/app/menulink.php';
    require_once SERVER_ROOT .'/app/module.php';
    require_once SERVER_ROOT .'/app/cms.php';

    // Smarty
    require_once SERVER_ROOT .'/app/vendor/smarty/smarty/libs/Smarty.class.php';
    
    // Import model
    require_once SERVER_ROOT .'/app/model/mdl_blog.inc.php';
    
    // Generic controller class
    require_once SERVER_ROOT .'/app/controller/generic_controller.inc.php';
    
    // Import view functions
    require_once SERVER_ROOT .'/app/view/page_header.php';


    // Store the configuration
    BlogCMS::addToConfig($config);

/****************************************************************
  Set-Up Hooks
****************************************************************/    

    // Import all modules
    // todo - cache in database
    // make a UI for enabling / disabling modules
    // ... and a CLI script?
    $directoryListing = new \DirectoryIterator(SERVER_ROOT . '/app/modules');

    foreach ($directoryListing as $file) {
        if ($file->isDir() && $file->getFilename() != '.' && $file->getFilename() != '..') {
            
            $dirPath = $file->getPath() .'/'. $file->getFilename();

            if (!file_exists($dirPath .'/info.json')) continue;

            $moduleInfo = JSONhelper::JSONFileToArray($dirPath . '/info.json');
            if ($moduleInfo['enabled'] != 1) continue;

            BlogCMS::registerModule($file->getFilename());
        }
    }