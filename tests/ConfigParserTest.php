<?php
declare(strict_types=1);

namespace App\Tests;

use App\Services\ConfigParser;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\Yaml\Exception\ParseException;

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
            __DIR__ . '/fixtures/testFile1.config.json',
            __DIR__ . '/fixtures/testFile2.config.json'
        );
        $configParser->mergeData();

        $this->assertTrue(json_encode($expectedOutput) === json_encode($configParser->getMergedContent()));
    }

    public function testTraversalCorrect(): void
    {
        $configParser = new ConfigParser();
        $configParser->loadFiles(
            __DIR__ . '/fixtures/testFile1.config.json',
            __DIR__ . '/fixtures/testFile2.config.json'
        );
        $configParser->mergeData();

        $expectedOutput = '127.0.0.1';

        $this->assertTrue($expectedOutput === $configParser->traverseContent('database.host'));
    }

    public function testTraversalIncorrect(): void
    {
        $configParser = new ConfigParser();
        $configParser->loadFiles(
            __DIR__ . '/fixtures/config.local.json',
            __DIR__ . '/fixtures/config.json'
        );
        $configParser->mergeData();

        $expectedOutput = '127.0.0.1';

        $this->assertFalse($expectedOutput === $configParser->traverseContent('database.HOST'));
    }

    public function testYamlFiles(): void
    {
        $expectedOutput = [
            'environment' => 'development',
            'database' => [
                'host' => 'yaml host'
            ]
        ];

        $configParser = new ConfigParser();
        $configParser->loadFiles(
            __DIR__.'/fixtures/testFile1.config.yaml',
            __DIR__.'/fixtures/testFile2.config.yaml'
        );
        $configParser->mergeData();

        $this->assertTrue(json_encode($expectedOutput) === json_encode($configParser->getMergedContent()));
    }

    public function testYamlAndJson(): void
    {
        $expectedOutput = [
            'environment' => 'prod',
            'database' => [
                'host' => 'mysql'
            ]
        ];

        $configParser = new ConfigParser();
        $configParser->loadFiles(
            __DIR__.'/fixtures/testFile2.config.yaml',
            __DIR__.'/fixtures/testFile1.config.json'
        );
        $configParser->mergeData();

        $this->assertTrue(json_encode($expectedOutput) === json_encode($configParser->getMergedContent()));
    }

    /**
     * @throws ParseException
     */
    public function testInvalidYaml(): void
    {
        $this->expectException(ParseException::class);
        $configParser = new ConfigParser();
        $configParser->loadFiles(
            __DIR__ . '/fixtures/testFileInvalid.yaml'
        );
    }

    /**
     * @throws ParseException
     */
    public function testInvalidJson(): void
    {
        $this->expectException(ParseException::class);
        $configParser = new ConfigParser();
        $configParser->loadFiles(
            __DIR__ . '/fixtures/testFileInvalid.json'
        );
    }

    /**
     * @throws FileNotFoundException
     */
    public function testFileNotFound(): void
    {
        $this->expectException(FileNotFoundException::class);
        $configParser = new ConfigParser();
        $configParser->loadFiles(
            __DIR__ . '/fixtures/testFileInvalid123.json'
        );
    }

    /**
     * @throws FileNotFoundException
     */
    public function testDeepNestedFilesString(): void
    {
        $expectedOutput = 'test';
        $configParser = new ConfigParser();
        $configParser->loadFiles(
            __DIR__ . '/fixtures/deeplyNestedConfig1.json',
            __DIR__ . '/fixtures/deeplyNestedConfig2.json'
        );
        $configParser->mergeData();
        $traversedData = $configParser->traverseContent('database.data.json.config');
        $this->assertTrue($expectedOutput ===  $traversedData);
    }

    /**
     * @throws FileNotFoundException
     */
    public function testDeepNestedFilesArray(): void
    {
        $expectedOutput = [
            'deeply' => 'test',
            'json' => [
                'config' => 'test',
                'test' => [
                    'final' => 'final'
                ]
            ]
        ];

        $configParser = new ConfigParser();
        $configParser->loadFiles(
            __DIR__ . '/fixtures/deeplyNestedConfig1.json',
            __DIR__ . '/fixtures/deeplyNestedConfig2.json'
        );
        $configParser->mergeData();
        $traversedData = $configParser->traverseContent('database.data');
        $this->assertTrue(json_encode($expectedOutput) ===  $traversedData);
    }

    /**
     * @throws FileNotFoundException
     */
    public function testMultipleFiles(): void
    {
        $expectedOutput = 'yaml host';
        $configParser = new ConfigParser();
        $configParser->loadFiles(
            __DIR__ . '/fixtures/deeplyNestedConfig1.json',
            __DIR__ . '/fixtures/deeplyNestedConfig2.json',
            __DIR__ . '/fixtures/testFile2.config.yaml',
        );
        $configParser->mergeData();
        $traversedData = $configParser->traverseContent('database.host');
        $this->assertTrue($expectedOutput ===  $traversedData);
    }
}
