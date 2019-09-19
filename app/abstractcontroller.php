<?php

namespace rbwebdesigns\HamletCMS;

class GenericController
{
    /**
     * @var \rbwebdesigns\core\Request
     */
    protected $request;
    /**
     * @var \rbwebdesigns\core\Response
     */
    protected $response;
    
    public function __construct() 
    {
        $this->request = HamletCMS::request();
        $this->response = HamletCMS::response();
    }

    public function defaultAction() {}
    
}
