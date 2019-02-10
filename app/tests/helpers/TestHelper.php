<?php

class TestHelper
{

    public static $cms = null;

    /**
     * Includes all php files from the src/tests directory
     */
    public static function includeFiles()
    {
        foreach (glob(__DIR__ .'/src/tests/*.php') as $filePath) {
            require $filePath;
        }
    }
    
}