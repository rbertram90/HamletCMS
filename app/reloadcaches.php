<?php

namespace rbwebdesigns\HamletCMS;

require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/envsetup.inc.php';

HamletCMS::generateRouteCache();
HamletCMS::generateMenuCache();
HamletCMS::generatePermissionCache();
HamletCMS::generateSmartyTemplateCache();