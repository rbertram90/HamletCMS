<?php
namespace rbwebdesigns;

class Router{
    private $routes;
    
    function __construct($proutes) {
		$this->routes = $proutes;
    }
    
    public function lookup($query) {
        if(array_key_exists($query, $this->routes)) {
           return $this->routes[$query];
        }
        else return false;
    }
}
?>