<?php

namespace Sdon2\GitAutoDeploy\exceptions;

use Sdon2\GitAutoDeploy\views\errors\InvalidDeployFile;
use Monolog\Logger;

class InvalidDeployFileException extends BaseException {
    public static function build($offendingCommand, Logger $logger): self {
        return new self(new InvalidDeployFile($offendingCommand), $logger);
    }

    public function getStatusCode(): int {
        return 422;
    }
}
