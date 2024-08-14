<?php

namespace Sdon2\GitAutoDeploy\views\errors;

use Sdon2\GitAutoDeploy\views\BaseView;

class Forbidden extends BaseView {
    public function render(): string {
        return "<span style=\"color: #ff0000\">Sorry, no hamster - better convince your parents!</span>\n";
    }
}
