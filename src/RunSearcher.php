<?php

namespace Sdon2\GitAutoDeploy;

use Ramsey\Uuid\Uuid;

class RunSearcher {
    private $logFileName;
    private $configReader;

    public function __construct(ConfigReader $configReader, ?string $logFileName = null) {
        $this->logFileName = $logFileName;
        $this->configReader = $configReader;
    }

    public function search(string $runId, ?array $fields = null): array {
        $logContents = $this->read($runId);
        $foundRows = [];
        foreach (explode("\n", $logContents) as $logRow) {
            if (strpos($logRow, $runId) !== false) {
                $parsedRow = $this->parse($logRow);
                if ($fields && !empty($fields)) {
                    $parsedRow = array_intersect_key($parsedRow, array_flip($fields));
                }
                $foundRows [] = $parsedRow;
            }
        }
        return $foundRows;
    }

    private function parse(string $logRow): array {
        $context = explode(' - ', $logRow);
        $jsonContext = count($context) === 2 ? @$context[1] : null;
        if (!$this->isJson($jsonContext)) {
            $jsonContext = null;
        }
        $date = @$context[0];
        $logLevel = null;
        $message = null;
        if (!$jsonContext) {
            $pattern = '/\[(?P<date>.+?)\]\s+(?P<loggerName>\S+)\.(?P<level>\S+):\s+(?P<message>.+?)\s+(?P<context>\{.*\})(?P<extra>.+)/';
            if (preg_match($pattern, $logRow, $matches, PREG_OFFSET_CAPTURE, 0)) {
                $matches = array_filter($matches, "is_string", ARRAY_FILTER_USE_KEY);
                $date = $matches['date'][0];
                $logLevel = $matches['level'][0];
                $message = $matches['message'][0];
                $jsonContext = $matches['context'][0];
                $extraContext = trim($matches['extra'][0]);
            }
        }
        $result = @json_decode($jsonContext, true) ?? [];
        $result['date'] = $date;
        $result['logLevel'] = $logLevel;
        $result['message'] = $message;
        $result['extra_context'] = @json_decode($extraContext, true) ?? [];
        if ($this->configReader->get(ConfigReader::EXPOSE_RAW_LOG)) {
            $result['raw_log'] = $logRow;
        }
        return $result;
    }

    private function isJson(?string $input): bool {
        json_decode($input);
        return json_last_error() === JSON_ERROR_NONE;
    }

    private function read(string $runId): string {
        if (!Uuid::isValid($runId)) {
            return '';
        }
        $fileName = escapeshellarg($this->logFileName ?? $this->fileName());
        $runId = escapeshellarg($runId);
        exec("cat {$fileName} | grep {$runId}", $searchOutput);
        return implode("\n", $searchOutput);
    }

    private function fileName(): string {
        return implode(DIRECTORY_SEPARATOR, [
            __DIR__,
            '..',
            'deploy-log.log',
        ]);
    }
}
