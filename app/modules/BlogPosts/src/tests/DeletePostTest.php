<?php

namespace rbwebdesigns\blogcms\BlogPosts\tests;

use rbwebdesigns\blogcms\tests\TestResult;
use rbwebdesigns\blogcms\BlogPosts\controller\PostsAPI;

class DeletePostTest extends TestResult {

    // Variables which need to be populated
    // before run method called
    public $blogID = 0;
    public $postToDelete = 0;

    public function run()
    {
        $this->checkRequirements();

        $controller = new PostsAPI();

        $this->request->setVariable('blogID', $this->blogID);
        $this->request->setVariable('postID', $this->postToDelete);

        $controller->delete();

        $responseBody = $this->response->getBody();
        $result = json_decode($responseBody, true);

        if ($result['success'] === true) {
            print "Info: DeletePostTest Passed" . PHP_EOL;
        }
        else {
            print "Error: DeletePostTest failed - " . $result['errorMessage'] . PHP_EOL;
            exit;
        }
    }

    protected function checkRequirements()
    {
        if ($this->blogID == 0) {
            print "BlogID not set for DeletePostTest" . PHP_EOL;
            exit;
        }
        if ($this->postToDelete == 0) {
            print "postToDelete not set for DeletePostTest" . PHP_EOL;
            exit;
        }
    }

}