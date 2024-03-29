<?php
namespace HamletCMS;

use Athens\CSRF;
use rbwebdesigns\core\JSONhelper;
use HamletCMS\HamletCMS;

/****************************************************************
  Install Entry point
****************************************************************/

    // Include cms setup script
    require_once __DIR__ . '/setup.inc.php';
    
    $request = HamletCMS::request();
    $response = HamletCMS::response();

    $coreModules = scandir(SERVER_MODULES_PATH);
    $modules = [];

    foreach ($coreModules as $module) {
        if ($module == '.' || $module == '..' || $module == 'tests') continue;

        if (!is_dir(SERVER_MODULES_PATH. '/' . $module)) continue; // Could be wrong version?!

        $info = JSONhelper::JSONFileToArray(SERVER_MODULES_PATH . "/{$module}/info.json");

        $modules[$module] = [
            'core' => 1,
            'locked' => $info['locked']
        ];
    }

    if (file_exists(SERVER_ADDONS_PATH . '/modules')) {
        $addonModules = scandir(SERVER_ADDONS_PATH . '/modules');
        
        foreach ($addonModules as $module) {
            if ($module == '.' || $module == '..') continue;
        
            $info = JSONhelper::JSONFileToArray(SERVER_ADDONS_PATH . "/modules/{$module}/info.json");
        
            $modules[$module] = [
                'core' => 0,
                'description' => $info['description'] ?? '',
                'dependencies' => $info['dependencies'] ?? [],
                'locked' => 0 // addon modules cannot be locked
            ];
        }
    }

    if ($request->method() == 'POST') {

        $dbc = HamletCMS::databaseConnection();

        // Create modules table
        $dbc->query("CREATE TABLE `modules` (
            `name` varchar(30) NOT NULL,
            `description` text,
            `core` tinyint(1) NOT NULL DEFAULT '0',
            `enabled` tinyint(1) NOT NULL DEFAULT '0',
            `locked` tinyint(1) NOT NULL DEFAULT '0',
            `settings` text,
            `dbversion` int(4) NOT NULL DEFAULT '0'
          ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

        $dbc->query("ALTER TABLE `modules` ADD UNIQUE KEY `name` (`name`);");

        // Create procedures
        $dbc->query("CREATE DEFINER=CURRENT_USER FUNCTION `wordcount` (`str` TEXT) RETURNS INT(11) NO SQL
            DETERMINISTIC
            SQL SECURITY INVOKER
        BEGIN
            DECLARE wordCnt, idx, maxIdx INT DEFAULT 0;
            DECLARE currChar, prevChar BOOL DEFAULT 0;
            SET maxIdx=char_length(str);
            WHILE idx < maxIdx DO
                SET currChar=SUBSTRING(str, idx, 1) RLIKE '[[:alnum:]]';
                IF NOT prevChar AND currChar THEN
                    SET wordCnt=wordCnt+1;
                END IF;
                SET prevChar=currChar;
                SET idx=idx+1;
            END WHILE;
            RETURN wordCnt;
          END");

        // Run module install
        foreach ($modules as $key => $module) {

            if (!array_key_exists('locked', $module)) $module['locked'] = 0;

            // Run through all the optional modules that were chosen not to
            // be installed and insert module row into database
            if (!$module['locked']) {
                $install = $request->getString($key);
                if ($install != 'on') {
                    $dbc->insertRow("modules", [
                        'name' => $key,
                        'enabled' => 0,
                        'locked' => 0,
                        'core' => $module['core'],
                        'description' => $module['description']
                    ]);
                    continue;
                }
            }

            $subFolder = $module['core'] ? 'core' : 'addon';
            $className = '\\HamletCMS\\'. $key . '\\Module';
            if (class_exists($className)) {
                $class = new $className();
                if (method_exists($class, 'install')) {
                    $class->install();
                }
            }

            $dbc->insertRow("modules", [
                'name' => $key,
                'enabled' => 1,
                'locked' => $module['locked'],
                'description' => $module['description'] ?? '',
                'core' => $module['core']
            ]);

            HamletCMS::registerModule($key);
        }

        HamletCMS::generateModelAliasCache();
        HamletCMS::generateRouteCache();
        HamletCMS::generateMenuCache();
        HamletCMS::generatePermissionCache();
        HamletCMS::generateSmartyTemplateCache();

        HamletCMS::runHook('onReloadCache', []);

        // $adminController = new \HamletCMS\SiteAdmin\controller\SiteAdmin();
        // $adminController->reloadCache(false);

        // Create admin user
        $accountData = [
            'firstname'       => $request->getString('fld_name'),
            'surname'         => $request->getString('fld_surname'),
            'gender'          => $request->getString('fld_gender'),
            'username'        => $request->getString('fld_username'),
            'password'        => $request->getString('fld_password'),
            'passwordConfirm' => $request->getString('fld_password_2'),
            'email'           => $request->getString('fld_email'),
            'emailConfirm'    => $request->getString('fld_email_2'),
            'admin'           => 1
        ];

        /** @var \HamletCMS\UserAccounts\model\UserAccounts  */
        $modelUsers = HamletCMS::model('useraccounts');

        // Misc folders
        if (!file_exists(SERVER_AVATAR_FOLDER)) {
            mkdir(SERVER_AVATAR_FOLDER);
            mkdir(SERVER_AVATAR_FOLDER.'/thumbs');
        }

        // Validate
        if ($accountData['email'] != $accountData['emailConfirm']
            || $accountData['password'] != $accountData['passwordConfirm']) {
            $response->redirect('/cms/install', 'Email or passwords did not match', 'error');
        }

        $checkUser = $modelUsers->get('id', ['username' => $accountData['username']], '', '', false);
        if ($checkUser && $checkUser['id']) {
            $response->redirect('/cms/install', 'Username is already taken', 'error');
        }

        if (!$modelUsers->register($accountData)) {
            $response->redirect('/cms/install', 'Error creating admin account', 'error');
        }

        $response->redirect('/cms', 'Installation complete', 'success');
    }

    $response->setVar('messages', HamletCMS::session()->getAllMessages());
    $response->setVar('config', HamletCMS::config());
    $response->setVar('modules', $modules);
    $response->write('install.tpl');

    // Check form submissions for CSRF token
    CSRF::init();