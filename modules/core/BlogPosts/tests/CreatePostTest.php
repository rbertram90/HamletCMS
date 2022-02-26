<?php

namespace HamletCMS\BlogPosts\tests;

use HamletCMS\tests\TestResult;
use HamletCMS\BlogPosts\controller\PostsAPI;

/**
 * Inherited variables:
 *   protected $request
 *   protected $response
 */
class CreatePostTest extends TestResult
{
    public $blogID = 0;
    public $postID = 0;

    /**
     * @todo This uses the standard post to test but right now
     * this isn't necessarily installed. Need to make this a mandatory module
     * with the option to hide?
     */
    public function run() {
        print "Info: Running test create post". PHP_EOL;
        $controller = new PostsAPI();

        $this->request->setVariable('blogID', $this->blogID);
        $this->request->setVariable('date', date('Y-m-d'));
        $this->request->setVariable('title', 'Test blog post');
        $this->request->setVariable('content', 'Some content');
        $this->request->setVariable('summary', 'Blog post summary');
        $this->request->setVariable('tags', 'hello,world');
        $this->request->setVariable('teaserimage', '');
        $this->request->setVariable('draft', 0);
        $this->request->setVariable('type', 'standard');

        $controller->create();

        $responseBody = $this->response->getBody();
        $outcome = json_decode($responseBody, true);

        $this->postID = $outcome['post']['id'];

        print "Created post with ID = " . $this->postID . PHP_EOL;

        if ($outcome['success']) {
            print "Info: Test passed". PHP_EOL;
        }
        else {
            print "Error: Test failed - " . $errorMessage . PHP_EOL;
            exit;
        }
    }
}