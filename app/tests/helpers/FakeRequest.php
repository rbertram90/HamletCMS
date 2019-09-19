<?php
namespace rbwebdesigns\HamletCMS\tests;

use rbwebdesigns\core\Request;

class FakeRequest extends Request
{
    // Allow the method to be overridden
    public $method = 'GET';

    /**
     * Allow request variables to be set manually
     * 
     * @param string $name
     * @param mixed $value
     */
    public function setVariable($name, $value)
    {
        $_REQUEST[$name] = $value;
    }

    public function method()
    {
        return $this->method;
    }
}