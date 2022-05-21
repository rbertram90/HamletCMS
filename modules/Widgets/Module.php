<?php

namespace HamletCMS\Widgets;

class Module {

    /**
     * Hook onModuleInstalled
     */
    public function onModuleInstalled($args) {
        controller\WidgetsAdmin::reloadWidgetCache();
    }

    /**
     * Hook onModuleUninstalled
     */
    public function onModuleUninstalled($args) {
        controller\WidgetsAdmin::reloadWidgetCache();
    }

    /**
     * Hook onReloadCache
     */
    public function onReloadCache($args) {
        controller\WidgetsAdmin::reloadWidgetCache();
    }

}