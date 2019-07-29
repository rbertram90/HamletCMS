<?php

namespace rbwebdesigns\blogcms\BlogPosts\tests;

use rbwebdesigns\blogcms\tests\TestResult;
use rbwebdesigns\blogcms\BlogPosts\controller\PostsAPI;

class ClonePostTest extends TestResult {

    // Variables which need to be populated
    // before run method called
    public $blogID = 0;
    public $postToClone = 0;

    public function run()
    {
        $this->checkRequirements();

        $controller = new PostsAPI();

        $this->request->setVariable('blogID', $this->blogID);
        $this->request->setVariable('postID', $this->postToClone);

        $controller->clonePost();

        $responseBody = $this->response->getBody();
        $result = json_decode($responseBody, true);

        if ($result['success'] === true) {
            print "Info: ClonePostTest Passed" . PHP_EOL;
        }
        else {
            print "Error: ClonePostTest failed - " . $result['errorMessage'] . PHP_EOL;
            exit;
        }
    }

    protected function checkRequirements()
    {
        if ($this->blogID == 0) {
            print "BlogID not set for ClonePostTest" . PHP_EOL;
            exit;
        }
        if ($this->postToClone == 0) {
            print "postToClone not set for ClonePostTest" . PHP_EOL;
            exit;
        }
    }

}