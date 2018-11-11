<?php

namespace rbwebdesigns\blogcms\SiteAdmin\controller;

use rbwebdesigns\blogcms\GenericController;
use rbwebdesigns\blogcms\BlogCMS;

class SiteAdmin extends GenericController
{
    protected $model;

    public function __construct()
    {
        parent::__construct();

        // Important check if user is admin!
        $currentUser = BlogCMS::session()->currentUser;

        if (!$currentUser || $currentUser['admin'] != 1) {
            $this->response->redirect('/');
        }

        $this->model = BlogCMS::model('\rbwebdesigns\blogcms\SiteAdmin\model\Modules');
    }

    public function modules()
    {
        $this->response->setTitle('All modules');
        $this->response->setVar('modules', $this->model->getList());
        $this->response->write('modulelist.tpl', 'SiteAdmin');
    }

    public function reloadCache()
    {
        BlogCMS::generateRouteCache();
        BlogCMS::generateMenuCache();
        BlogCMS::generatePermissionCache();
        BlogCMS::generateTemplateCache();
        $this->response->redirect('/cms/admin/modules', 'System caches reloaded', 'success');
    }

    public function newModuleScan()
    {
        $modulesList = [];

        if ($handle = opendir(SERVER_MODULES_PATH)) {
            // Get all directories
            while (false !== ($folder = readdir($handle))) {
                if (strpos($folder, '.') !== false) continue;
                if (!file_exists(SERVER_MODULES_PATH .'/'. $folder .'/info.json')) continue;
                $modulesList[] = $folder;
            }
            closedir($handle);

            $dbModules = $this->model->getList();
            $deleteCount = 0;
            $addCount = 0;

            foreach ($dbModules as $module) {
                if (($key = array_search($module['name'], $modulesList)) !== FALSE) {
                    // Found in database - remove from list
                    array_splice($modulesList, $key, 1);
                }
                else {
                    // Module removed from file system!
                    $this->model->delete(['name' => $module['name']]);
                    $deleteCount++;
                }
            }

            foreach ($modulesList as $module) {
                // Ones that remain are new
                $insert = $this->model->insert(['name' => $module, 'enabled' => 0, 'locked' => 0]);
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

        $modules = $this->model->getList();
        $updates = [];

        foreach ($modules as $module) {
            $currentVersion = $module['dbversion'];
            $moduleFile = SERVER_MODULES_PATH .'/'. $module['name'] .'/'. $module['name'] .'.php';

            if (!file_exists($moduleFile)) continue;
            
            require_once $moduleFile;

            $className = '\\rbwebdesigns\\blogcms\\'. $module['name'];
            $mainClass = new $className();
            $updateIndex = $currentVersion + 1;

            while (method_exists($mainClass, 'databaseUpdate'. $updateIndex)) {
                $updateIndex++;
            }

            if ($updateIndex-1 != $currentVersion) {
                $updates[] = [
                    'name' => $module['name'],
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
        $modules = $this->model->getList();
        
        foreach ($modules as $module) {
            $currentVersion = $module['dbversion'];
            $moduleFile = SERVER_MODULES_PATH .'/'. $module['name'] .'/'. $module['name'] .'.php';

            if (!file_exists($moduleFile)) continue;
            
            require_once $moduleFile;

            $className = '\\rbwebdesigns\\blogcms\\'. $module['name'];
            $mainClass = new $className();
            $updateIndex = $currentVersion + 1;

            while (method_exists($mainClass, 'databaseUpdate'. $updateIndex)) {
                $methodName = 'databaseUpdate'. $updateIndex;
                $mainClass->$methodName();
                $updateIndex++;
            }

            // Note update was done
            $this->model->update(['name' => $module['name']], ['dbversion' => $updateIndex-1]);
        }

        $this->response->redirect('/cms/admin/modules', 'Database updates done', 'success');
    }
}