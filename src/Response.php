<?php

namespace Sdon2\GitAutoDeploy;

use Sdon2\GitAutoDeploy\views\BaseView;

class Response {
    public const STATUS_MAP = [
        200 => 'OK',
        400 => 'Bad Request',
        403 => 'Forbidden',
    ];

    private $body = '';
    private $statusCode = 200;
    private $runId;

    public function __construct(string $runId) {
        $this->runId = $runId;
    }

    public function getRunId(): string {
        return $this->runId;
    }

    public function addViewToBody(BaseView $view): void {
        $this->addToBody($view->render());
    }

    public function addToBody(string $contents): void {
        $this->body .= $contents;
    }

    public function setStatusCode(int $statusCode): void {
        $this->statusCode = $statusCode;
    }

    public function send(string $contentType = 'text/html;charset=UTF-8'): void {
        header("Content-Type: {$contentType}");
        header(
            sprintf(
                "HTTP/1.1 %s %s",
                $this->statusCode,
                self::STATUS_MAP[$this->statusCode]
            )
        );
        echo $this->body;
    }
}
