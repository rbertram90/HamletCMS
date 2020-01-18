<?php

namespace rbwebdesigns\HamletCMS\tests;

class TestHelper
{

    public static $cms = null;

    /**
     * Includes all php files from the src/tests directory
     */
    public static function includeFiles($module)
    {
        foreach (glob(SERVER_MODULES_PATH . "/core/{$module}/src/tests/*.php") as $filePath) {
            require $filePath;
        }
        foreach (glob(SERVER_MODULES_PATH . "/addon/{$module}/src/tests/*.php") as $filePath) {
            require $filePath;
        }
    }
    
}