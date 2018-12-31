<?php

namespace rbwebdesigns\blogcms\Widgets;

use rbwebdesigns\blogcms\BlogCMS;
use rbwebdesigns\core\JSONHelper;

class AbstractWidget
{
    protected $request;
    protected $response;
    protected $blog;
    protected $config = null;

    public function __construct()
    {
        $this->request = BlogCMS::request();
        $this->response = BlogCMS::response();

        BlogCMS::$blogID = $this->request->getInt('blogID');
        $this->blog = BlogCMS::getActiveBlog();
    }

    public function config()
    {
        if (is_null($this->config)) {
            $file = SERVER_PATH_BLOGS ."/{$this->blog->id}/widgets.json";
            if (file_exists($file)) {
                $this->config = JSONhelper::JSONFileToArray($file);
            }
        }
        return $this->config;
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