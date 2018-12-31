<?php

namespace rbwebdesigns\blogcms\Widgets\widgets;

use rbwebdesigns\blogcms\Widgets\AbstractWidget;

class CustomHTML extends AbstractWidget
{

    public function render() {
        $this->response->write('customHTML.tpl', 'Widgets');
    }

}