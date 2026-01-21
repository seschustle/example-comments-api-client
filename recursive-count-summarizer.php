<?php
/**
 * @author Pavel Lovkii <plovkiy@yandex.ru>
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/seschustle/example-comments-api-client
 */

declare(strict_types=1);

function summarizeCounts(string $dirPath): int
{
    if (!is_dir($dirPath)) {
        die(sprintf('Error: %s does not exist or is not a directory.' . PHP_EOL, $dirPath));
    }

    echo 'Starting count summarization in directory: ' . $dirPath . PHP_EOL;
    $dirIterator = new RecursiveDirectoryIterator($dirPath, RecursiveDirectoryIterator::SKIP_DOTS);

    $sum = 0;
    foreach ($dirIterator as $file) {
        if ($file->isDir()) {
            echo 'Found inner directory: ' . $dirPath . PHP_EOL;
            $sum += summarizeCounts($file->getRealPath());
        }
        
        if ($file->isFile() && $file->getFilename() === 'count') {
            
            $content = file_get_contents($file->getRealPath());
            preg_match_all('/\d+/', $content, $matches);
            $countFileSum = array_reduce(
                $matches[0], 
                fn(int $carry, string $number) => $carry + (int) $number,
                0
            );
            echo 'Found count file in ' . $dirPath . '. The count is ' . $countFileSum . PHP_EOL;
            $sum += $countFileSum;
        }
    }

    return $sum;
}

echo summarizeCounts(__DIR__ . '/' . $argv[1]) . PHP_EOL;