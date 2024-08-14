<?php

namespace Sdon2\GitAutoDeploy\views;

abstract class BaseView {
    abstract public function render(): string;

    public function __tostring(): string {
        return $this->render();
    }
}
