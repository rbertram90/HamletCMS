<?php

namespace HamletCMS\SiteAdmin\controller;

use HamletCMS\GenericController;
use HamletCMS\HamletCMS;
use HamletCMS\Module;

class SiteAdmin extends GenericController
{
    public function __construct()
    {
        parent::__construct();

        // Important check if user is admin!
        $currentUser = HamletCMS::session()->currentUser;

        if (!$currentUser || $currentUser['admin'] != 1) {
            $this->response->redirect('/');
        }
    }

    public function modules()
    {
        $this->response->setTitle('All modules');
        $this->response->setVar('modules', $this->model('modules')->getList());
        $this->response->write('modulelist.tpl', 'SiteAdmin');
    }

    public function reloadCache($redirect = true)
    {
        HamletCMS::generateModelAliasCache();
        HamletCMS::generateRouteCache();
        HamletCMS::generateMenuCache();
        HamletCMS::generatePermissionCache();
        HamletCMS::generateSmartyTemplateCache();
        HamletCMS::runHook('onReloadCache', []);

        if ($redirect) {
            $location = $_SERVER['HTTP_REFERER'] ?: '/cms/admin/modules';
            $this->response->redirect($location, 'System caches rebuilt', 'success');
        }
    }

    public function newModuleScan()
    {
        if ($coreModules = scandir(SERVER_MODULES_PATH . '/core')) {
            $modulesList = [];
            
            // Get all directories
            foreach ($coreModules as $folder) {
                if (strpos($folder, '.') !== false) continue;
                if (!file_exists(SERVER_MODULES_PATH . "/core/{$folder}/info.json")) continue;
                $modulesList[$folder] = 'core';
            }

            if ($addonModules = scandir(SERVER_MODULES_PATH . '/addon')) {
                foreach ($addonModules as $folder) {
                    if (strpos($folder, '.') !== false) continue;
                    if (!file_exists(SERVER_MODULES_PATH . "/addon/{$folder}/info.json")) continue;
                    $modulesList[$folder] = 'addon';
                }
            }

            $dbModules = $this->model('modules')->getList();
            $deleteCount = 0;
            $addCount = 0;

            foreach ($dbModules as $module) {
                if (key_exists($module->name, $modulesList)) {
                    // Found in database - remove from list
                    unset($modulesList[$module->name]);
                }
                else {
                    // Module removed from file system!
                    $this->model('modules')->delete(['name' => $module->name]);
                    $deleteCount++;
                }
            }

            foreach ($modulesList as $name => $status) {
                // Ones that remain are new
                $core = $status == 'core';
                $insert = $this->model('modules')->insert(['name' => $name, 'enabled' => 0, 'locked' => 0, 'core' => $core]);
                if (!$insert) $this->response->redirect('/cms/admin/modules', 'Unable to update database', 'error');
                $addCount++;
            }

            $this->response->redirect('/cms/admin/modules', $addCount .' modules added, '. $deleteCount. ' modules removed', 'success');
        }

        $this->response->redirect('/cms/admin/modules', 'Unable to read module directory', 'error');
    }

    /**
     * Handles GET /cms/admin/updatedatabase
     */
    public function databaseChecker()
    {
        if ($this->request->method() == 'POST') {
            return $this->runDatabaseUpdates();
        }

        $modules = $this->model('modules')->getList();
        $updates = [];

        foreach ($modules as $module) {
            $currentVersion = $module->dbversion;
            $folder = $module->core ? 'core' : 'addon';
            $moduleFile = SERVER_MODULES_PATH ."/{$folder}/{$module->name}/{$module->name}.php";

            if (!file_exists($moduleFile)) continue;
            
            require_once $moduleFile;

            $className = '\\HamletCMS\\'. $module->name;
            $mainClass = new $className();
            $updateIndex = $currentVersion + 1;

            while (method_exists($mainClass, 'databaseUpdate'. $updateIndex)) {
                $updateIndex++;
            }

            if ($updateIndex-1 != $currentVersion) {
                $updates[] = [
                    'name' => $module->name,
                    'current' => $currentVersion,
                    'latest' => $updateIndex-1
                ];
            }
        }

        if (count($updates) == 0) {
            $this->response->redirect('/cms/admin/modules', 'No updates pending', 'success');
        }
        else {
            $this->response->setTitle('Database updates');
            $this->response->setVar('modules', $updates);
            $this->response->write('pendingupdates.tpl', 'SiteAdmin');
        }
    }

    /**
     * Handles POST /cms/admin/updatedatabase
     */
    protected function runDatabaseUpdates()
    {
        $modules = $this->model('modules')->getList();
        
        foreach ($modules as $module) {
            $currentVersion = $module->dbversion;
            $folder = $module->core ? 'core' : 'addon';
            $moduleFile = SERVER_MODULES_PATH ."/{$folder}/{$module->name}/{$module->name}.php";

            if (!file_exists($moduleFile)) continue;
            
            require_once $moduleFile;

            $className = '\\HamletCMS\\'. $module->name;
            $mainClass = new $className();
            $updateIndex = $currentVersion + 1;

            while (method_exists($mainClass, 'databaseUpdate'. $updateIndex)) {
                $methodName = 'databaseUpdate'. $updateIndex;
                $mainClass->$methodName();
                $updateIndex++;
            }

            // Note update was done
            $this->model('modules')->update(['name' => $module->name], ['dbversion' => $updateIndex-1]);
        }

        $this->response->redirect('/cms/admin/modules', 'Database updates done', 'success');
    }

    /**
     * Handles GET /cms/admin/installmodule/[module_name]
     */
    public function installModule()
    {
        $moduleName = $this->request->getUrlParameter(1);
        $module = new Module($moduleName);

        // Run install method (if exists)
        if (!is_null($module) && !is_null($module->instance)) {
            if (method_exists($module->instance, 'install')) {
                $module->instance->install();
            }
        }

        // Update module database
        $this->model('modules')->update(['name' => $module->key], ['enabled' => 1]);

        // Run hook
        HamletCMS::runHook('onModuleInstalled', ['module' => $module]);

        $this->response->redirect('/cms/admin/modules', 'Module installed', 'success');
    }

    /**
     * Handles GET /cms/admin/uninstallmodule/[module_name]
     */
    public function uninstallModule()
    {
        $moduleName = $this->request->getUrlParameter(1);
        $module = HamletCMS::getModule($moduleName);

        // Run uninstall method (if exists)
        if (!is_null($module) && !is_null($module->instance)) {
            if (method_exists($module->instance, 'uninstall')) {
                $module->instance->uninstall();
            }
        }

        // Update module database
        $this->model('modules')->update(['name' => $module->key], ['enabled' => 0]);

        HamletCMS::runHook('onModuleUninstalled', ['module' => $module]);

        $this->response->redirect('/cms/admin/modules', 'Module uninstalled', 'success');
    }

    /**
     * Handles GET /cms/admin/php
     */
    public function phpInfo() {
      phpinfo();
      exit;
    }

}
