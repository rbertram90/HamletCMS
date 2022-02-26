<?php
namespace HamletCMS\Blog\tests;

use HamletCMS\HamletCMS;
use HamletCMS\tests\TestResult;

/**
 * Creates a new blog, stores the newly created ID in the $blogID variable, accessible
 * from the calling code so we can use this blog to run further tests
 */
class CreateBlogTest extends TestResult
{
    public $blogID = 0;

    public function run()
    {
        // Instantiate the blog controller
        $controller = new \HamletCMS\Blog\controller\Blogs();

        $this->log("Running test CreateBlog");

        // Set POST variables
        $this->request->setVariable('fld_blogname', 'Blog test '. time());
        $this->request->setVariable('fld_blogdesc', 'Automatically created by test suite');
        
        // Run the method
        $controller->runCreateBlog();
        
        // Check response
        if ($redirect = $this->response->redirect) {
            // Response was a redirect
            switch (strtolower($redirect['messageType'])) {
                case 'error':
                    print "Error: Test errored with message - ". $redirect['message'];
                    exit;
                case 'success':
                    $locationParts = explode('/', $redirect['location']);
                    $this->blogID = array_pop($locationParts);
                    HamletCMS::$blogID = $this->blogID;
                    
                    $this->log("Created blog #" . $this->blogID);
                    $this->log("Test passed");
                    break;
            }
        }
    }
}
