<?php

namespace rbwebdesigns\blogcms;

require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/envsetup.inc.php';

BlogCMS::generateRouteCache();
BlogCMS::generateMenuCache();