<?php
declare(strict_types=1);

namespace App\Services;

use App\Exception\ParseFileException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class ConfigParser
{
    private const JSON_EXT = 'json';

    private const YAML_EXT = 'yaml';

    /**
     * @var array
     */
    private array $fileData = [];

    /**
     * @var array
     */
    private array $mergedContent = [];

    /**
     * @param string ...$files
     * @return void
     * @throws ParseFileException|ParseException
     */
    public function loadFiles(string ...$files): void
    {
        foreach ($files as $file){
            $extension = pathinfo($file)['extension'];
            $fileContents = file_get_contents($file);

            $content = null;

            if ($extension === self::JSON_EXT){
                $content = json_decode($fileContents, true);
            }
            if ($extension === self::YAML_EXT){
                $content = Yaml::parse($fileContents);
            }

            if ($content === null){
                throw new ParseException('File content empty or file content is invalid');
            }

            $this->fileData[] = [
                'name' => $file,
                'content' => $content
            ];
        }
    }

    public function mergeData(): void
    {
        foreach ($this->fileData as $fileDatum){
            $this->mergeFileContent($fileDatum['content'], $this->mergedContent);
        }
    }

    /**
     * @return array
     */
    public function getMergedContent(): array
    {
        return $this->mergedContent;
    }

    /**
     * Due to specifying return type of string in this function
     * is easier to convert other possible outcomes to string for consistency
     * as spec requires to return data but does not say in what format
     *
     * @param string $destination
     * @return string|null
     */
    public function traverseContent(string $destination): ?string
    {
        if (count($this->mergedContent) < 1){
            return null;
        }

        $traversalIndices = explode('.', $destination);
        $traversedContent = null;

        foreach ($traversalIndices as $traversalIndex){
            if (!isset($this->mergedContent[$traversalIndex]) && $traversedContent === null){
                return null;
            }
            if ($traversedContent === null){
                $traversedContent = $this->mergedContent[$traversalIndex];
                continue;
            }
            if (!isset($traversedContent[$traversalIndex])){
                return null;
            }
            $traversedContent = $traversedContent[$traversalIndex];
        }

        if (is_array($traversedContent)){
            $traversedContent = json_encode($traversedContent);
        }
        if (is_numeric($traversedContent)){
            $traversedContent = (string) $traversedContent;
        }

        return $traversedContent;
    }

    /**
     * @param array $fileContent
     * @param array $mergedContent
     * @return void
     */
    private function mergeFileContent(array $fileContent, array &$mergedContent): void
    {
        if (count($mergedContent) < 1){
            $mergedContent = $fileContent;
            return;
        }

        foreach ($fileContent as $key => $content){
            if (is_array($content)){
                if (!isset($mergedContent[$key])){
                    $mergedContent[$key] = [];
                }
                $this->mergeFileContent($content, $mergedContent[$key]);
            }
            $mergedContent[$key] = $content;
        }
    }
}