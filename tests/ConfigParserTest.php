<?php
declare(strict_types=1);

namespace App\Tests;

use App\Services\ConfigParser;
use PHPUnit\Framework\TestCase;

class ConfigParserTest extends TestCase
{
    public function testParseTwoFiles(): void
    {
        $expectedOutput = [
            'environment' => 'development',
            'database' => [
                'host' => '127.0.0.1'
            ]
        ];

        $configParser = new ConfigParser();
        $configParser->loadFiles(
            __DIR__.'/testFixtures/testFile1.config.json',
            __DIR__.'/testFixtures/testFile2.config.json'
        );
        $configParser->mergeData();

        $this->assertTrue(json_encode($expectedOutput) === json_encode($configParser->getMergedContent()));
    }

    public function testTraversalCorrect(): void
    {
        $configParser = new ConfigParser();
        $configParser->loadFiles(
            __DIR__.'/testFixtures/testFile1.config.json',
            __DIR__.'/testFixtures/testFile2.config.json'
        );
        $configParser->mergeData();

        $expectedOutput = '127.0.0.1';

        $this->assertTrue($expectedOutput === $configParser->traverseContent('database.host'));
    }

    public function testTraversalIncorrect(): void
    {
        $configParser = new ConfigParser();
        $configParser->loadFiles(
            __DIR__.'/testFixtures/testFile1.config.json',
            __DIR__.'/testFixtures/testFile2.config.json'
        );
        $configParser->mergeData();

        $expectedOutput = '127.0.0.1';

        $this->assertFalse($expectedOutput === $configParser->traverseContent('database.HOST'));
    }

    public function testYamlFiles()
    {
        $expectedOutput = [
            'environment' => 'development',
            'database' => [
                'host' => '127.0.0.1'
            ]
        ];

        $configParser = new ConfigParser();
        $configParser->loadFiles(
            __DIR__.'/testFixtures/testFile1.config.yaml',
            __DIR__.'/testFixtures/testFile2.config.yaml'
        );
        $configParser->mergeData();

        $this->assertTrue(json_encode($expectedOutput) === json_encode($configParser->getMergedContent()));
    }
}
