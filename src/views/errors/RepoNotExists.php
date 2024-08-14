<?php

namespace Sdon2\GitAutoDeploy\views\errors;

use Sdon2\GitAutoDeploy\views\BaseView;

class RepoNotExists extends BaseView {
    public function render(): string {
        return "<span style=\"color: #ff0000\">Given repo does not exists</span>\n";
    }
}
