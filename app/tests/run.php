<?php
    namespace rbwebdesigns\blogcms\tests;

    // 0 = User ID
    if (!isset($argv[1]) || $argv[1] <= 0) {
        print "Please pass a user ID as first argument";
        exit;
    }

    // Run the same setup as with normal requests
    require __DIR__ .'/../setup.inc.php';

    // Arguments
    error_reporting(E_ALL);

    // Require all helper files
    foreach (glob(__DIR__ .'/helpers/*.php') as $filePath) {
        require $filePath;
    }

    // Provide fake request and response objects
    // $request = BlogCMS::request();
    // $response = BlogCMS::response();

    require __DIR__ .'/1_create_blog.php';

    print "Done!";