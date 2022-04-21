<?php
declare(strict_types=1);

namespace App\Services;

class ConfigParser
{
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
     */
    public function loadFiles(string ...$files): void
    {
        foreach ($files as $file){
            $content = json_decode(file_get_contents($file), true);

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
        dd($this->mergedContent);
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