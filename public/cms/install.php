<?php
namespace rbwebdesigns\blogcms;

use Athens\CSRF;

/****************************************************************
  Install Entry point
****************************************************************/

    // Include cms setup script
    require_once __DIR__ . '/../../app/setup.inc.php';

        
    $request = BlogCMS::request();
    $response = BlogCMS::response();

    $modules = [
        'API' => ['optional' => 0],
        'Blog' => ['optional' => 0],
        'BlogPosts' => ['optional' => 0],
        'BlogView' => ['optional' => 0],
        'Contributors' => ['optional' => 0],
        'FavouriteBlogs' => ['optional' => 0],
        'Files' => ['optional' => 0],
        'Settings' => ['optional' => 0],
        'SiteAdmin' => ['optional' => 0],
        'UserAccounts' => ['optional' => 0],
        'Website' => ['optional' => 0],
        'EventLogger' => ['optional' => 1],
        'LayoutPost' => ['optional' => 1],
        'MarkdownPost' => ['optional' => 1],
        'PostComments' => ['optional' => 1],
        'VideoPost' => ['optional' => 1],
    ];

    if ($request->method() == 'POST') {

        if (!file_exists(SERVER_ROOT . '/public/blogdata')) {
            $blogdata = mkdir(SERVER_ROOT . '/public/blogdata');
            if (!$blogdata) die('Unable to create directory for blog data - check /public directory permissions');
        }

        $dbc = BlogCMS::databaseConnection();

        // Create modules table
        $dbc->query("CREATE TABLE `modules` (
            `name` varchar(30) NOT NULL,
            `description` text NOT NULL,
            `enabled` tinyint(1) NOT NULL DEFAULT '0',
            `locked` tinyint(1) NOT NULL DEFAULT '0',
            `settings` text NOT NULL,
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
            if ($module['optional']) {
                $install = $request->getString($key);
                if ($install != 'on') {
                    $dbc->insertRow("modules", ['name' => $key, 'enabled' => 0,'locked' => 0]);
                    continue;
                }
            }

            $classPath = SERVER_ROOT .'/app/modules/'. $key .'/'. $key .'.php';
            if (file_exists($classPath)) {
                require_once $classPath;
                $className = '\\rbwebdesigns\\blogcms\\'. $key;
                $class = new $className();
                if (method_exists($class, 'install')) {
                    $class->install();
                }
            }

            $dbc->insertRow("modules", [
                'name' => $key,
                'enabled' => 1,
                'locked' => !$module['optional']
            ]);

            BlogCMS::registerModule($key);
        }

        BlogCMS::generateRouteCache();
        BlogCMS::generateMenuCache();
        BlogCMS::generatePermissionCache();
        BlogCMS::generateTemplateCache();

        // $adminController = new \rbwebdesigns\blogcms\SiteAdmin\controller\SiteAdmin();
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

        $modelUsers = BlogCMS::model('\rbwebdesigns\blogcms\UserAccounts\model\UserAccounts');

        // Validate
        if ($accountData['email'] != $accountData['emailConfirm']
            || $accountData['password'] != $accountData['passwordConfirm']) {
            $response->redirect('/cms/install.php', 'Email or passwords did not match', 'error');
        }

        $checkUser = $modelUsers->get('id', ['username' => $accountData['username']], '', '', false);
        if($checkUser && $checkUser['id']) {
            $response->redirect('/cms/install.php', 'Username is already taken', 'error');
        }

        if (!$modelUsers->register($accountData)) {
            $response->redirect('/cms/install.php', 'Error creating admin account', 'error');
        }

        $response->redirect('/cms', 'Installation complete', 'success');
    }

    $response->setVar('messages', BlogCMS::session()->getAllMessages());
    $response->setVar('config', BlogCMS::config());
    $response->setVar('modules', $modules);
    $response->write('install.tpl');

    // Check form submissions for CSRF token
    CSRF::init();