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
        foreach (glob(SERVER_MODULES_PATH . "/{$module}/tests/*.php") as $filePath) {
            require $filePath;
        }
        foreach (glob(SERVER_ADDONS_PATH . "/modules/{$module}/tests/*.php") as $filePath) {
            require $filePath;
        }
    }
    
}