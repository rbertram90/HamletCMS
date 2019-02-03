<?php
namespace rbwebdesigns\blogcms\tests;
use rbwebdesigns\blogcms\BlogCMS;

// Create and assign custom test request and response classes
// so as to not actually trigger a redirect
$request = new FakeRequest();
$response = new FakeResponse();
BlogCMS::request($request);
BlogCMS::response($response);

// Assign the user passed into the script through CLI
BlogCMS::session()->currentUser = [
    'id' => $argv[1],
    'admin' => 1
];

// Instantiate the blog controller
$controller = new \rbwebdesigns\blogcms\Blog\controller\Blogs();

// ------------ Start create blog test ------------
print "Running test 1_create_blog". PHP_EOL;
$response->testResult = new TestResult();
$request->setVariable('fld_blogname', 'Blog test '. time());
$request->setVariable('fld_blogdesc', 'Automatically created by test suite');

$controller->runCreateBlog();

if ($redirect = $response->testResult->redirect) {
    // Response was a redirect
    switch (strtolower($redirect['messageType'])) {
        case 'error':
            print "Test errored with message - ". $redirect['message'];
            exit;
        case 'success':
            print "Test passed". PHP_EOL;
            break;
    }
}
// ------------ End create blog test ------------