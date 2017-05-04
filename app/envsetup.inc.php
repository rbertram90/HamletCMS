<?php
/****************************************************************
  Session Handling
****************************************************************/
    
    // Start Session if not already started
    if(!isset($_SESSION)) session_start();
    
    
/****************************************************************
  Core Includes
****************************************************************/

    // Language file
    require_once SERVER_ROOT . '/app/core/lang/en.inc.php';
    
    // Helper function library
    require_once SERVER_ROOT . '/app/core/core.php';
    
    
/****************************************************************
  Database Constants
****************************************************************/

    // Load config - will probabily move this out of app folder!    
    $jsonhelper = new \rbwebdesigns\JSONhelper();
    $config = $jsonhelper->jsonToArray(SERVER_ROOT . '/app/config/config.json');
    
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
    $cms_db = new rbwebdesigns\db($databaseCredentials['server'], $databaseCredentials['user'], $databaseCredentials['password'], $databaseCredentials['name']);
    

/****************************************************************
  Includes
****************************************************************/

    // Smarty
    require_once SERVER_ROOT.'/app/vendor/smarty/smarty/libs/Smarty.class.php';
    
    // Import model
    require_once SERVER_ROOT.'/app/model/mdl_blog.inc.php';
    require_once SERVER_ROOT.'/app/model/mdl_post.inc.php';
    require_once SERVER_ROOT.'/app/model/mdl_comment.inc.php';
    require_once SERVER_ROOT.'/app/model/mdl_contributor.inc.php';
    
    // Generic controller class
    require_once SERVER_ROOT.'/app/controller/generic_bcms_controller.inc.php';
    
    // Generic view
    require_once SERVER_ROOT.'/app/view/view.php';
    
    // Import view functions
    require_once SERVER_ROOT.'/app/view/page_header.php';

?>