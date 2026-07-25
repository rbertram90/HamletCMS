<?php
namespace HamletCMS\tests;

/**
 * Creates and destroys a throwaway MySQL database for the test suite.
 */
class TestDatabase
{
    /** @var \PDO  connection to the server, no database selected */
    protected $pdo;

    /** @var array  database credentials from config.json */
    protected $config;

    /**
     * @param array $databaseConfig
     *   The "database" block from config.json (server, user, password, name).
     */
    public function __construct(array $databaseConfig)
    {
        $this->config = $databaseConfig;

        $this->pdo = new \PDO(
            'mysql:host=' . $databaseConfig['server'],
            $databaseConfig['user'],
            $databaseConfig['password']
        );
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Build a safe test database name derived from the configured name.
     * Guaranteed to differ from the real database.
     *
     * @return string
     */
    public function testDatabaseName()
    {
        return $this->config['name'] . '_test';
    }

    /**
     * Drop any leftover copy and create a fresh, empty test database.
     *
     * @return string  The name of the database that was created.
     */
    public function create()
    {
        $name = $this->guardedName();

        $this->pdo->exec("DROP DATABASE IF EXISTS `{$name}`");
        $this->pdo->exec("CREATE DATABASE `{$name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

        return $name;
    }

    /**
     * Remove the test database.
     */
    public function drop()
    {
        $name = $this->guardedName();
        $this->pdo->exec("DROP DATABASE IF EXISTS `{$name}`");
    }

    /**
     * Return the test database name, refusing to ever target the real one.
     *
     * @return string
     * @throws \RuntimeException
     */
    protected function guardedName()
    {
        $name = $this->testDatabaseName();

        if ($name === $this->config['name'] || substr($name, -5) !== '_test') {
            throw new \RuntimeException(
                "Refusing to operate on '{$name}' - the test database must differ "
                . "from the configured database and end in '_test'."
            );
        }

        return $name;
    }
}
