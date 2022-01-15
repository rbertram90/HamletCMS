<?php

namespace HamletCMS;

use HamletCMS\HamletCMS;

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

    /**
     * Shortcut to get a Model
     * 
     * @param string $model
     * 
     * @return \rbwebdesigns\core\RBFactory Model
     */
    protected function model($model) {
        return HamletCMS::model($model);
    }
    
}
