<?php
namespace rbwebdesigns\blogcms;

use rbwebdesigns\core\JSONHelper;

/**
 * Wrapper class for an module
 */
class Module
{
    public $key;
    // public $enabled; // will always be 1?
    public $instance = null;

    protected $factory = null;

    // These should be set in the info.json folder
    // public $namespace;
    public $author;

    public function __construct($key)
    {
        $this->key = $key;

        // Get database details
        // Required?
        // $this->factory = BlogCMS::model('\rbwebdesigns\blogcms\SiteAdmin\model\Modules');
        // $this->factory->get(['name' => $key]);

        // load config
        $moduleInfo = JSONhelper::JSONFileToArray(SERVER_MODULES_PATH . '/' . $this->key . '/info.json');
        foreach ($moduleInfo as $propertykey => $property) {
            $this->$propertykey = $property;
        }

        if (file_exists(SERVER_MODULES_PATH . '/' . $this->key . '/' . $this->key . '.php')) {
            require_once SERVER_MODULES_PATH . '/' . $this->key . '/' . $this->key . '.php';
            $className = '\\rbwebdesigns\\blogcms\\' . $this->key;
            $this->instance = new $className();
        }
    }
}