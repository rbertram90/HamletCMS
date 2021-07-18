<?php
namespace HamletCMS;

use rbwebdesigns\core\JSONHelper;

/**
 * Wrapper class for an module
 */
class Module
{
    public $key;
    public $core;
    public $instance = null;
    protected $factory = null;

    // These should be set in the info.json folder
    // public $namespace;
    public $author;

    public function __construct($key)
    {
        $this->key = $key;

        $folder = file_exists(SERVER_MODULES_PATH . "/core/{$key}") ? 'core' : 'addon';
        $this->core = $folder === 'core';

        // load config
        $moduleInfo = JSONhelper::JSONFileToArray(SERVER_MODULES_PATH . "/{$folder}/{$this->key}/info.json");
        foreach ($moduleInfo as $propertykey => $property) {
            $this->$propertykey = $property;
        }
        
        $className = '\\HamletCMS\\' . $this->key . '\\Module';
        if (class_exists($className)) {
            $this->instance = new $className();
        }
    }
}