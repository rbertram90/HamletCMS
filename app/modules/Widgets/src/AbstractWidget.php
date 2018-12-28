<?php

namespace rbwebdesigns\blogcms\Widgets;

use rbwebdesigns\blogcms\BlogCMS;

class AbstractWidget
{
    protected $request;
    protected $response;
    protected $blog;

    public function __construct()
    {
        $this->request = BlogCMS::request();
        $this->response = BlogCMS::response();

        BlogCMS::$blogID = $this->request->getInt('blogID');
        $this->blog = BlogCMS::getActiveBlog();
    }

    /**
     * render is the function called to get the HTML
     * output for a widget. It is expected for this
     * function to get the data required and then pass
     * to a smarty template.
     */
    public function render() {
        
    }
}