<?php
namespace HamletCMS\tests;

use rbwebdesigns\core\JSONhelper;
use HamletCMS\HamletCMS;

/**
 * Runs a full Hamlet CMS install into the currently connected database.
 */
class TestInstaller
{
    /**
     * Install every core and addon module into the test database and create an
     * administrator account.
     *
     * @param array $adminAccount
     *   Optional overrides for the admin account fields.
     *
     * @return int  The id of the created admin user.
     */
    public static function install(array $adminAccount = [])
    {
        $dbc = HamletCMS::databaseConnection();

        self::createBaseSchema($dbc);
        self::installModules($dbc);
        self::generateCaches();

        return self::createAdmin($adminAccount);
    }

    /**
     * Create the "modules" registry table and the wordcount SQL function.
     */
    protected static function createBaseSchema(\rbwebdesigns\core\ObjectDatabase $dbc)
    {
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
    }

    /**
     * Discover core + addon modules, run each module's install() and register
     * it in the modules table.
     */
    protected static function installModules(\rbwebdesigns\core\ObjectDatabase $dbc)
    {
        $modules = self::discoverModules();

        // Register modules first
        foreach ($modules as $key => $module) {
            $dbc->insertRow('modules', [
                'name' => $key,
                'enabled' => 1,
                'locked' => $module['locked'],
                'description' => $module['description'] ?? '',
                'core' => $module['core'],
            ]);

            HamletCMS::registerModule($key);
        }

        // Now run install logic - by getting module classes.
        foreach (HamletCMS::$modules as $key => $module) {
            $moduleLogicClass = $module->getInstance();

            if (method_exists($moduleLogicClass, 'install')) {
                $moduleLogicClass->install();
            }
        }
    }

    /**
     * Build the list of installable modules (core first, then addons).
     *
     * @return array  keyed by module directory name.
     */
    protected static function discoverModules()
    {
        $modules = [];

        foreach (scandir(SERVER_MODULES_PATH) as $module) {
            if ($module === '.' || $module === '..' || $module === 'tests') continue;
            if (!is_dir(SERVER_MODULES_PATH . '/' . $module)) continue;

            $info = JSONhelper::JSONFileToArray(SERVER_MODULES_PATH . "/{$module}/info.json");
            $modules[$module] = [
                'core' => 1,
                'description' => $info['description'] ?? '',
                'locked' => $info['locked'] ?? 0,
            ];
        }

        if (file_exists(SERVER_ADDONS_PATH . '/modules')) {
            foreach (scandir(SERVER_ADDONS_PATH . '/modules') as $module) {
                if ($module === '.' || $module === '..') continue;
                if (!is_dir(SERVER_ADDONS_PATH . '/modules/' . $module)) continue;

                $info = JSONhelper::JSONFileToArray(SERVER_ADDONS_PATH . "/modules/{$module}/info.json");
                $modules[$module] = [
                    'core' => 0,
                    'description' => $info['description'] ?? '',
                    'locked' => 0,
                ];
            }
        }

        return $modules;
    }

    /**
     * Regenerate all the JSON caches the application relies on for routing,
     * models, menus, permissions and templates.
     */
    protected static function generateCaches()
    {
        HamletCMS::generateModelAliasCache();
        HamletCMS::generateRouteCache();
        HamletCMS::generateMenuCache();
        HamletCMS::generatePermissionCache();
        HamletCMS::generateSmartyTemplateCache();

        HamletCMS::runHook('onReloadCache', []);
    }

    /**
     * Create an administrator account so tests have an authenticated user.
     *
     * @param array $overrides
     *
     * @return int  new user id
     */
    protected static function createAdmin(array $overrides = [])
    {
        // Some registration paths expect the avatar folders to exist.
        if (!file_exists(SERVER_AVATAR_FOLDER)) {
            mkdir(SERVER_AVATAR_FOLDER);
            mkdir(SERVER_AVATAR_FOLDER . '/thumbs');
        }

        $account = array_merge([
            'firstname' => 'Test',
            'surname'   => 'Admin',
            'gender'    => '',
            'username'  => 'testadmin',
            'password'  => 'password123',
            'email'     => 'testadmin@example.com',
            'admin'     => 1,
        ], $overrides);

        /** @var \HamletCMS\UserAccounts\model\UserAccounts $modelUsers */
        $modelUsers = HamletCMS::model('useraccounts');

        if (!$modelUsers->register($account)) {
            throw new \RuntimeException('Failed to create test admin account');
        }

        $user = $modelUsers->get('id', ['username' => $account['username']], '', '', false);

        return (int) $user->id;
    }
}
