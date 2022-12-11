<?php

namespace HamletCMS\Widgets;

use HamletCMS\HamletCMS;
use rbwebdesigns\core\JSONHelper;

abstract class AbstractWidget
{
    /** @var \rbwebdesigns\core\Request */
    protected $request;

    /** @var \HamletCMS\HamletCMSResponse */
    protected $response;

    /** @var \HamletCMS\Blog\Blog */
    protected $blog;

    protected $config = null;

    public $section;
    
    public $widget;

    public $referer;

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
            $file = SERVER_PATH_BLOGS . "/{$this->blog->id}/widgets.json";
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
    abstract public function render();

}
