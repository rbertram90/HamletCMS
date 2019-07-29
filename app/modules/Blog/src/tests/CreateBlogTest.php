<?php
namespace rbwebdesigns\blogcms\Blog\tests;

use rbwebdesigns\blogcms\BlogCMS;
use rbwebdesigns\blogcms\tests\TestResult;

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
        $controller = new \rbwebdesigns\blogcms\Blog\controller\Blogs();

        print "Info: Running test CreateBlog". PHP_EOL;

        $this->request->setVariable('fld_blogname', 'Blog test '. time());
        $this->request->setVariable('fld_blogdesc', 'Automatically created by test suite');
        
        $controller->runCreateBlog();
        
        if ($redirect = $this->response->redirect) {
            // Response was a redirect
            switch (strtolower($redirect['messageType'])) {
                case 'error':
                    print "Error: Test errored with message - ". $redirect['message'];
                    exit;
                case 'success':
                    print "Info: Test passed". PHP_EOL;
                    $locationParts = explode('/', $redirect['location']);
                    $this->blogID = array_pop($locationParts);
                    print "Created blog with ID = " . $this->blogID . PHP_EOL;
                    break;
            }
        }
    }
}
