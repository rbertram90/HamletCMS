<?php

namespace rbwebdesigns\HamletCMS;

class Widgets {

    /**
     * Hook onModuleInstalled
     */
    public function onModuleInstalled($args) {
        Widgets\controller\WidgetsAdmin::reloadWidgetCache();
    }

    /**
     * Hook onModuleUninstalled
     */
    public function onModuleUninstalled($args) {
        Widgets\controller\WidgetsAdmin::reloadWidgetCache();
    }

    /**
     * Hook onReloadCache
     */
    public function onReloadCache($args) {
        Widgets\controller\WidgetsAdmin::reloadWidgetCache();
    }

}