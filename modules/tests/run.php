<?php
    namespace HamletCMS\tests;

    use rbwebdesigns\core\JSONhelper;
    use HamletCMS\HamletCMS;

    /**
     * Test runner.
     *
     * Spins up a throwaway MySQL database, installs a full copy of the CMS into
     * it, runs the test suite against that clean install, then drops the
     * database again - regardless of how the run ends.
     *
     * Usage:  php app/modules/tests/run.php
     */

    if (php_sapi_name() !== 'cli') {
        die('The test runner can only be run from the command line.');
    }

    error_reporting(E_ALL);

/****************************************************************
  Bootstrap the throwaway database
****************************************************************/

    require_once __DIR__ . '/../../../vendor/autoload.php';
    require_once __DIR__ . '/helpers/TestDatabase.php';

    $rootConfig = JSONhelper::JSONFileToArray(__DIR__ . '/../../../config/config.json');

    $testDb = new TestDatabase($rootConfig['database']);
    $testDbName = $testDb->create();

    // Guarantee the database is removed even if a test calls exit() or a fatal
    // error is thrown part way through the run.
    register_shutdown_function(function () use ($testDb, $testDbName) {
        $testDb->drop();
        print "Dropped test database: {$testDbName}" . PHP_EOL;
    });

    // Point the application at the test database before it boots.
    putenv('HAMLET_DB_NAME=' . $testDbName);
    putenv('DISABLE_EMAIL=1');

/****************************************************************
  Boot the application against the test database
****************************************************************/

    // Runs config load, constant + session setup and connects to the (empty)
    // test database. In CLI, setup gracefully returns when it finds no install.
    // NB: nothing may be printed before this point or session_start() will warn
    // that headers have already been sent.
    require_once __DIR__ . '/../../setup.inc.php';

    print "Created test database: {$testDbName}" . PHP_EOL;

    // Load the remaining test helpers.
    foreach (glob(__DIR__ . '/helpers/*.php') as $filePath) {
        require_once $filePath;
    }

/****************************************************************
  Install the CMS and authenticate as the admin
****************************************************************/

    print "Installing CMS into test database..." . PHP_EOL;
    $adminId = TestInstaller::install();
    print "Installed. Admin user id: {$adminId}" . PHP_EOL;

    HamletCMS::session()->currentUser = [
        'id' => $adminId,
        'admin' => 1,
    ];

/****************************************************************
  Run the tests
****************************************************************/

    print "Running tests..." . PHP_EOL . PHP_EOL;
    HamletCMS::runHook('runUnitTests', ['context' => 'root']);

    print PHP_EOL . "Done!" . PHP_EOL;
