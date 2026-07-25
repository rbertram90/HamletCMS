<?php

namespace HamletCMS\tests;

use HamletCMS\HamletCMS;

abstract class TestResult implements TestResultInterface
{
    public $redirect = false;

    /** @var \HamletCMS\tests\FakeRequest */
    protected $request;

    /** @var \HamletCMS\tests\FakeResponse */
    protected $response;
    
    public function __construct()
    {
        // Create and assign custom test request and response classes
        // so as to not actually trigger a redirect
        $this->request = new FakeRequest();
        $this->response = new FakeResponse();
        HamletCMS::request($this->request);
        HamletCMS::response($this->response);
    }
    
    /**
     * Run a controller action, stopping cleanly at the first redirect the same
     * way a real request would (a real redirect ends in exit()). The redirect
     * details remain available afterwards on $this->response->redirect.
     *
     * @param callable $action
     *   Typically a closure that calls the controller method under test,
     *   e.g. $this->invoke(fn() => $controller->runCreateBlog());
     */
    protected function invoke(callable $action)
    {
        try {
            $action();
        }
        catch (RedirectException $e) {
            // Expected - a redirect halts the request.
        }
    }

    /**
     * Output text to the console
     */
    protected function log(string $message)
    {
        print "Debug: " . $message . PHP_EOL;
    }

    /**
     * 
     */
    protected function fail(string $message) {
        print "TEST FAILED: " . $message . PHP_EOL;
        exit;
    }

}