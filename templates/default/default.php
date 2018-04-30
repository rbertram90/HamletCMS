<?php

$isHTTPS = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off';
$path = explode('\\', dirname(__FILE__));

define('BLOG_KEY', end($path));
define('CUSTOM_DOMAIN', true);

if ($isHTTPS) {
    define('DOMAIN', 'https://' . $_SERVER['SERVER_NAME']);
}
else {
    define('DOMAIN', 'http://' . $_SERVER['SERVER_NAME']);
}

require_once __DIR__ . '/../../../setup.inc.php';

require_once SERVER_ROOT . "/app/blog_setup.inc.php";