<?php

namespace Sdon2\GitAutoDeploy;

interface ISecurity {
    public function setParams(...$params): self;

    public function assert(): void;
}
