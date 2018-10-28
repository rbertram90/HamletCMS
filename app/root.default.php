<?php
/**
 * This file is automatically created with the first blog.
 * 
 * This configuration is read when using custom domains as $_SERVER['DOCUMENT_ROOT'] will
 * point to the blog folder.
 * 
 * I would rather have done it using environment variable in (apache/server) config
 * however this won't always be possible when using cpanel hosting package.
 * 
 * Could have also used a relative path to go up several levels but think this is more a
 * strong more stable solution if want to move the blogdata folder? 
 */

// Absolute path to the top level folder
define('SERVER_ROOT', '{SERVER_ROOT}');

// Domain name for the CMS
// This is for generic resources (JS, CSS, Images)
define('CMS_DOMAIN', '{CMS_DOMAIN}');

// Flag that we are using a custom domain
define('CUSTOM_DOMAIN', true);