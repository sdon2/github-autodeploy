<?php

namespace Sdon2\GitAutoDeploy\exceptions;

class ForbiddenException extends BaseException {
    public function getStatusCode(): int {
        return 403;
    }
}
