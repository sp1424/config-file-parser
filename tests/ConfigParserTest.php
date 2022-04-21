<?php

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
}
