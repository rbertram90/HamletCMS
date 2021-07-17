<?php

namespace HamletCMS\Widgets\widgets;

use HamletCMS\Widgets\AbstractWidget;

class CustomHTML extends AbstractWidget
{

    public function render() {
        $this->response->write('customHTML.tpl', 'Widgets');
    }

}