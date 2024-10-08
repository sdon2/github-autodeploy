<?php

namespace Sdon2\GitAutoDeploy\views\errors;

use Sdon2\GitAutoDeploy\views\BaseView;

class UnknownError extends BaseView {
    private $message;

    public function __construct(string $message = null) {
        $this->message = $message;
    }

    public function render(): string {
        return "<span style=\"color: #ff0000\">Unknown error making the hamster run:"
            . "  <span>{$this->message}</span>\n"
            . "</span>\n";
    }
}
