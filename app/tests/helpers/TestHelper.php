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
        foreach (glob(SERVER_MODULES_PATH .'/'. $module .'/src/tests/*.php') as $filePath) {
            require $filePath;
        }
    }
    
}