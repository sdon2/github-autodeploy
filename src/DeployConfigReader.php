<?php

namespace Sdon2\GitAutoDeploy;

use Sdon2\GitAutoDeploy\exceptions\InvalidDeployFileException;
use Monolog\Logger;
use Symfony\Component\Yaml\Exception\ParseException as YamlException;
use Symfony\Component\Yaml\Yaml;

class DeployConfigReader {
    public const CUSTOM_CONFIG_FILE_NAME = '.git-auto-deploy';

    private $configReader;
    private $logger;

    public function __construct(ConfigReader $configReader, Logger $logger) {
        $this->configReader = $configReader;
        $this->logger = $logger;
    }

    public function fetchRepoConfig(string $repoName): ?object {
        $repoConfigFileName = implode(DIRECTORY_SEPARATOR, [
            $this->configReader->get(ConfigReader::REPOS_BASE_PATH),
            $repoName,
            self::CUSTOM_CONFIG_FILE_NAME,
        ]);
        try {
            if (file_exists("$repoConfigFileName.json")) {
                $contents = json_decode(file_get_contents("$repoConfigFileName.json"), true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \JsonException(json_last_error_msg());
                }
                $this->logConfigPerRepoFound($repoName, 'json');
                return $this->generateConfig($contents);
            } elseif (file_exists("$repoConfigFileName.yaml")) {
                $this->logConfigPerRepoFound($repoName, 'yaml');
                $contents = Yaml::parse(file_get_contents("$repoConfigFileName.yaml"));
                return $this->generateConfig($contents);
            } elseif (file_exists("$repoConfigFileName.yml")) {
                $this->logConfigPerRepoFound($repoName, 'yml');
                $contents = Yaml::parse(file_get_contents("$repoConfigFileName.yml"));
                return $this->generateConfig($contents);
            }
        } catch (\JsonException $e) {
            $this->logger->error($e->getMessage());
            return null;
        } catch (YamlException $e) {
            $this->logger->error($e->getMessage());
            return null;
        }
        return null;
    }

    private function generateConfig(array $contents): object {
        $this->assertCommands($contents);
        return new class ($contents) {
            private $configData;

            public function __construct(array $configData) {
                $this->configData = $configData;
            }

            public function customCommands(): array {
                return $this->configData[ConfigReader::CUSTOM_UPDATE_COMMANDS] ?? [];
            }

            public function postFetchCommands(): array {
                return $this->configData[ConfigReader::POST_FETCH_COMMANDS] ?? [];
            }

            public function preFetchCommands(): array {
                return $this->configData[ConfigReader::PRE_FETCH_COMMANDS] ?? [];
            }
        };
    }

    private function logConfigPerRepoFound(string $repoName, string $extension): void {
        $this->logger->info("Using config file " . self::CUSTOM_CONFIG_FILE_NAME . ".$extension for repo {$repoName}");
    }

    private function assertCommands(array $commandsData): void {
        foreach ($commandsData as $commandsList) {
            foreach ($commandsList as $command) {
                if (!is_string($command)) {
                    throw InvalidDeployFileException::build($command, $this->logger);
                }
            }
        }
    }
}
