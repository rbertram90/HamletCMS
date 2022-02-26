<?php

namespace HamletCMS;

use rbwebdesigns\core\Files;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/envsetup.inc.php';

$config = HamletCMS::config();
$server_root = $config['environment']['root_directory'];
$public_root = $config['environment']['public_directory'];

if (!is_dir($public_root)) {
    mkdir($public_root, 0777, true);
}

// Check the first file copy, should be safe from there on in?
if (!copy(__DIR__ . '/public/index.php', $public_root . '/index.php')) {
    print "Failed to copy index.php, check directory permissions.";
    exit;
}
copy(__DIR__ . '/public/.htaccess', $public_root . '/.htaccess');

// Save where the backend application is
file_put_contents($public_root . '/hamlet.json', json_encode(['application_directory' => $server_root]));

// Check folders
if (!file_exists($public_root . '/hamlet')) {
    mkdir($public_root . '/hamlet');
}
if (!file_exists($public_root . '/hamlet/blogdata')) {
    mkdir($public_root . '/hamlet/blogdata'); // blog data
}
if (!file_exists($public_root . '/hamlet/avatars')) {
    mkdir($public_root . '/hamlet/avatars'); // user avatars
}

Files::xcopy(__DIR__ . '/public/js', $public_root . '/hamlet/js');
Files::xcopy(__DIR__ . '/public/css', $public_root . '/hamlet/css');
Files::xcopy(__DIR__ . '/public/images', $public_root . '/hamlet/images');
Files::xcopy(__DIR__ . '/public/fonts', $public_root . '/hamlet/fonts');
Files::xcopy(__DIR__ . '/public/resources', $public_root . '/hamlet/resources');

print "Done!" . PHP_EOL;