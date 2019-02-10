<?php
    namespace rbwebdesigns\blogcms\tests;

    use rbwebdesigns\blogcms\BlogCMS;

    // 0 = User ID
    if (!isset($argv[1]) || $argv[1] <= 0) {
        print "Error: Please pass a user ID as first argument".PHP_EOL;
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
    
    // Assign the user passed into the script through CLI
    BlogCMS::session()->currentUser = [
        'id' => $argv[1],
        'admin' => 1
    ];

    require __DIR__ .'/CreateBlogTest.php';

    $createBlogTest = new CreateBlogTest();
    $createBlogTest->run();

    // Run every other test!
    BlogCMS::runHook('runTests', ['blogID' => $createBlogTest->blogID]);

    print "Done!";