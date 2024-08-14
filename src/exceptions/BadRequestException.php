<?php

namespace Sdon2\GitAutoDeploy\exceptions;

class BadRequestException extends BaseException {
    public function getStatusCode(): int {
        return 400;
    }
}
