<?php

namespace Sdon2\GitAutoDeploy;

interface ILoggerDriver {
    public function write(string $content, string $date): void;
}
