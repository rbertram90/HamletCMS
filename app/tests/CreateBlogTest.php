<?php
namespace rbwebdesigns\blogcms\tests;

use rbwebdesigns\blogcms\BlogCMS;

/**
 * This test has been seperated out from modules directory as it is essential to run and
 * extract the newly created BlogID from as all other tests will likely need this to have
 * run.
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
                    break;
            }
        }
    }
}
