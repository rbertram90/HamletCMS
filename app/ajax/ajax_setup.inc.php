<?php
    namespace rbwebdesigns\blogcms;
    use rbwebdesigns;

    // Start session
    session_start();
    
    // Get setup
    require_once dirname(__FILE__).'/../../../root.inc.php';

    // Require Environment setup
    require_once SERVER_ROOT.'/envsetup.inc.php';
    
    // Create Posts Model
    $mdl_posts = new ClsPost($cms_db);

    // Create Blogs Model
    $mdl_blogs = new ClsBlog($cms_db);

    $mdl_users = new rbwebdesigns\Users($rb_db);
?>