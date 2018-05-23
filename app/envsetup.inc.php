<?php
namespace rbwebdesigns\blogcms;

use rbwebdesigns\core\Database;

/****************************************************************
  Session Handling
****************************************************************/
    
    // Start Session if not already started
    if (!isset($_SESSION)) session_start();
    
    if (IS_DEVELOPMENT) {
        error_reporting(E_STRICT);
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

    require_once SERVER_ROOT . '/app/view/response.php';
    require_once SERVER_ROOT . '/app/cms.php';

    // Smarty
    require_once SERVER_ROOT.'/app/vendor/smarty/smarty/libs/Smarty.class.php';
    
    // Import model
    require_once SERVER_ROOT.'/app/model/mdl_eventlog.inc.php';
    require_once SERVER_ROOT.'/app/model/mdl_blog.inc.php';
    require_once SERVER_ROOT.'/app/model/mdl_post.inc.php';
    require_once SERVER_ROOT.'/app/model/mdl_comment.inc.php';
    require_once SERVER_ROOT.'/app/model/mdl_contributor.inc.php';
    require_once SERVER_ROOT.'/app/model/mdl_contributorgroups.inc.php';
    require_once SERVER_ROOT.'/app/model/mdl_account.inc.php';
    
    // Generic controller class
    require_once SERVER_ROOT.'/app/controller/generic_controller.inc.php';
    
    // Import view functions
    require_once SERVER_ROOT.'/app/view/page_header.php';
