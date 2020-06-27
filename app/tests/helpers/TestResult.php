<?php

namespace rbwebdesigns\HamletCMS\tests;

use rbwebdesigns\HamletCMS\HamletCMS;

abstract class TestResult implements TestResultInterface
{
    public $redirect = false;

    /** @var \rbwebdesigns\HamletCMS\tests\FakeRequest */
    protected $request;

    /** @var \rbwebdesigns\HamletCMS\tests\FakeResponse */
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
     * Output text to the console
     */
    protected function log(string $message)
    {
        print "Debug: " . $message . PHP_EOL;
    }

}