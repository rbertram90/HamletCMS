<?php
namespace HamletCMS;

use rbwebdesigns\core\JSONHelper;
use stdClass;

/**
 * Wrapper class for an module
 */
class Module
{
    public $key;
    public $core;
    protected $instance = null;
    protected $factory = null;

    // These should be set in the info.json folder
    // public $namespace;
    public $author;

    public function __construct($key)
    {
        $this->key = $key;

        $folder = file_exists(SERVER_MODULES_PATH . "/{$key}") ? SERVER_MODULES_PATH : SERVER_ADDONS_PATH . '/modules';
        $this->core = $folder === SERVER_MODULES_PATH;

        // load config
        $moduleInfo = JSONhelper::JSONFileToArray("{$folder}/{$this->key}/info.json");
        foreach ($moduleInfo as $propertykey => $property) {
            $this->$propertykey = $property;
        }
    }

    public function getInstance() {
        if (!empty($this->instance)) {
            return $this->instance;
        }

        $className = '\\HamletCMS\\' . $this->key . '\\Module';
        if (class_exists($className)) {
            $this->instance = new $className();
        }
        else {
            $this->instance = new stdClass;
        }

        return $this->instance;
    }
}