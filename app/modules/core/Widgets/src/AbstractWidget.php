<?php

namespace rbwebdesigns\HamletCMS\Widgets;

use rbwebdesigns\HamletCMS\HamletCMS;
use rbwebdesigns\core\JSONHelper;

class AbstractWidget
{
    protected $request;
    protected $response;
    protected $blog;
    protected $config = null;

    public $section;
    public $widget;

    public function __construct()
    {
        $this->request = HamletCMS::request();
        $this->response = HamletCMS::response();

        HamletCMS::$blogID = $this->request->getInt('blogID');
        $this->blog = HamletCMS::getActiveBlog();
    }

    public function config()
    {
        if (is_null($this->config)) {
            $file = SERVER_PATH_BLOGS ."/{$this->blog->id}/widgets.json";
            if (file_exists($file)) {
                $widgetsConfig = JSONhelper::JSONFileToArray($file);
                $this->config = $widgetsConfig[$this->section][$this->widget];
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