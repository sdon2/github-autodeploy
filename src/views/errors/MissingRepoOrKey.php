<?php

namespace Sdon2\GitAutoDeploy\views\errors;

use Sdon2\GitAutoDeploy\views\BaseView;

class MissingRepoOrKey extends BaseView {
    public function render(): string {
        return "<span style=\"color: #ff0000\">Error reading hook no repo or key passed</span>\n";
    }
}
