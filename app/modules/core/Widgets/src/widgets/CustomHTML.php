<?php

namespace rbwebdesigns\HamletCMS\Widgets\widgets;

use rbwebdesigns\HamletCMS\Widgets\AbstractWidget;

class CustomHTML extends AbstractWidget
{

    public function render() {
        $this->response->write('customHTML.tpl', 'Widgets');
    }

}