<?php
namespace HamletCMS\tests;

use rbwebdesigns\core\Request;

class FakeRequest extends Request
{
    // Allow the method to be overridden
    public $method = 'GET';

    /**
     * Set a $_REQUEST variable
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

    public function setUrlParameter($index, $value) {
        $this->urlParameters[$index] = $value;
    }

}
