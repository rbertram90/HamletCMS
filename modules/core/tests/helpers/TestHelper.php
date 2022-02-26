<?php

namespace HamletCMS\tests;

class TestHelper
{

    public static $cms = null;

    /**
     * Includes all php files from the src/tests directory - still needed with psr-4?
     */
    public static function includeFiles($module)
    {
        foreach (glob(SERVER_MODULES_PATH . "/core/{$module}/tests/*.php") as $filePath) {
            require $filePath;
        }
        foreach (glob(SERVER_MODULES_PATH . "/addon/{$module}/tests/*.php") as $filePath) {
            require $filePath;
        }
    }
    
}