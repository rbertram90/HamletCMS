<?php

namespace HamletCMS\Widgets\controller;

use HamletCMS\HamletCMS;
use rbwebdesigns\core\JSONhelper;
use HamletCMS\GenericController;

class WidgetsView extends GenericController
{
    /**
     * @var HamletCMS\Blog;
     */
    protected $blog;

    public function __construct()
    {
        $this->blog = HamletCMS::getActiveBlog();
        parent::__construct();
    }

    /**
     * This function generates the placeholders
     * on the blog view which will load in the
     * real widget content via ajax. This is for
     * user experience benefits.
     */
    public function generatePlaceholders()
    {
        $widgetsConfig = $this->getWidgetConfig();
        $widgets = [];
        $currentPost = HamletCMS::getActivePost();

        if (CUSTOM_DOMAIN) {
            $config = HamletCMS::config();
            $cmsDomain = $config['environment']['canonical_domain'];
        }
        else {
            $cmsDomain = '';
        }

        foreach ($widgetsConfig as $section => $childWidgets) {
            $sectionlower = strtolower($section);
            $widgets[$sectionlower] = '';
            foreach ($childWidgets as $key => $widget) {
                $id = $sectionlower .'_'. $key;
                $widgets[$sectionlower].= "<div id='{$id}' class='widget'></div>";
                $params = [
                    "blogID" => $this->blog->id,
                    "section" => $section,
                    "widget" => $key,
                    "postID" => is_null($currentPost) ? null : $currentPost->id
                ];
                $params = json_encode($params);
                $widgets[$sectionlower].= "<script>
                    $('#{$id}').html('<img src=\"/images/ajax-loader.gif\">');
                    $.get('{$cmsDomain}/widgets/render', {$params}, function(response) {
                        $('#{$id}').html(response);
                    });
                </script>";
            }
        }

        return $widgets;
    }

    /**
     * New widget generator
     * @todo complete
     */
    public function generateWidget()
    {
        $blog = $this->request->getInt('blogID');
        $section = $this->request->getString('section');
        $widget = $this->request->getString('widget');

        HamletCMS::$blogID = $blog;
        $this->blog = HamletCMS::getActiveBlog();

        $widgetsConfig = $this->getWidgetConfig();
        
        if (!array_key_exists($section, $widgetsConfig)) {
            die('section "'.$section.'" not found');
        }
        if (!array_key_exists($widget, $widgetsConfig[$section])) {
            die('widget "'.$widget.'" not found');
        }

        $config = HamletCMS::config();
        if (CUSTOM_DOMAIN) {
            $cmsDomain = $config['environment']['canonical_domain'];
            $pathPrefix = '';
        }
        else {
            $cmsDomain = '';
            $pathPrefix = "/blogs/{$this->blog->id}";
        }

        $widgetCache = WidgetsAdmin::getInstalledWidgets();
        if (!array_key_exists($widget, $widgetCache)) {
            die('widget "'.$widget.'" not found in available widgets');
        }
        $className = $widgetCache[$widget]['class'];
        $widgetClass = new $className();
        foreach ($widgetsConfig[$section][$widget] as $name => $value) {
            $widgetClass->$name = $value;
            $this->response->setVar($name, $value);
        }
        $this->response->setVar('blog', $this->blog);

        $widgetClass->section = $section;
        $widgetClass->widget = $widget;
        $widgetClass->render();

        $this->request->isAjax = true;
    }

    protected function getWidgetConfig() {
        // Get JSON
        $widgetConfigPath = SERVER_PATH_BLOGS .'/'. $this->blog->id .'/widgets.json';
        $widgets = [];

        if(!file_exists($widgetConfigPath)) return [];
        return JSONhelper::JSONFileToArray($widgetConfigPath);
    }
}