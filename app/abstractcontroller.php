<?php

namespace rbwebdesigns\blogcms;

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
        $this->request = BlogCMS::request();
        $this->response = BlogCMS::response();
    }

    public function defaultAction() {}
    
}
