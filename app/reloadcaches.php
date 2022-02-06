<?php

namespace HamletCMS;

require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/envsetup.inc.php';

HamletCMS::generateModelAliasCache();
HamletCMS::generateRouteCache();
HamletCMS::generateMenuCache();
HamletCMS::generatePermissionCache();
HamletCMS::generateSmartyTemplateCache();

HamletCMS::runHook('onReloadCache', []);