<?php
namespace rbwebdesigns\blogcms;

use rbwebdesigns\core\JSONHelper;

/**
 * Wrapper class for an addon
 */
class Addon
{
    public $key;
    public $instance = null;

    // These should be set in the info.json folder
    public $namespace;
    public $author;

    public function __construct($key)
    {
        $this->key = $key;

        // load config
        $moduleInfo = JSONhelper::JSONFileToArray(SERVER_ADDONS_PATH . '/' . $this->key . '/info.json');
        foreach ($moduleInfo as $propertykey => $property) {
            $this->$propertykey = $property;
        }

        if (file_exists(SERVER_ADDONS_PATH . '/' . $this->key . '/' . $this->key . '.php')) {
            require_once SERVER_ADDONS_PATH . '/' . $this->key . '/' . $this->key . '.php';
            $className = $this->namespace . '\\' . $this->key;
            $this->instance = new $className();
        }
    }
}