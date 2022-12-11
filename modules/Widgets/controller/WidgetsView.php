<?php

namespace HamletCMS\Widgets\controller;

use HamletCMS\HamletCMS;
use HamletCMS\GenericController;
use rbwebdesigns\core\JSONhelper;

class WidgetsView extends GenericController
{
    /**
     * @var \HamletCMS\Blog\Blog;
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

        if (defined('CUSTOM_DOMAIN') && CUSTOM_DOMAIN) {
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
                    "postID" => is_null($currentPost) ? null : $currentPost->id,
                ];
                $params = json_encode($params);
                $widgets[$sectionlower].= "<script>
                    $('#{$id}').html('<img src=\"/hamlet/images/ajax-loader.gif\">');
                    $.get('{$cmsDomain}/widgets/render', {...{$params}, ...{referer: window.location.href}}, function(response) {
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
        $referer = $this->request->getString('referer');

        HamletCMS::$blogID = $blog;
        $this->blog = HamletCMS::getActiveBlog();

        $widgetsConfig = $this->getWidgetConfig();
        
        if (!array_key_exists($section, $widgetsConfig)) {
            die('section "'.$section.'" not found');
        }
        if (!array_key_exists($widget, $widgetsConfig[$section])) {
            die('widget "'.$widget.'" not found');
        }

        $widgetCache = HamletCMS::getCache('widgets');
        if (!array_key_exists($widget, $widgetCache)) {
            die('widget "'.$widget.'" not found in available widgets.');
        }
        $className = $widgetCache[$widget]['class'];
        $widgetClass = new $className();
        $settings = $widgetsConfig[$section][$widget] ?? [];

        if (method_exists($widgetClass, 'defaultSettings')) {
            $settings = array_merge($widgetClass->defaultSettings(), $settings);
        }

        foreach ($settings as $name => $value) {
            $widgetClass->$name = $value;
            $this->response->setVar($name, $value);
        }
        $this->response->setVar('blog', $this->blog);

        $widgetClass->section = $section;
        $widgetClass->widget = $widget;
        $widgetClass->referer = $referer;
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