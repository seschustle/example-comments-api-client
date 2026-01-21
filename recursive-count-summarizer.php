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
        throw new InvalidArgumentException("$dirPath does not exist or is not a directory.");
    }

    $dirIterator = new RecursiveDirectoryIterator($dirPath, RecursiveDirectoryIterator::SKIP_DOTS);
    $sum = 0;

    foreach (scandir($dirPath) as $child) {
        if ($child === '.' || $child === '..') {
            continue;
        }
        
        if (is_dir($dirPath .'/'. $child)) {
            $sum += summarizeCounts($dirPath .'/'. $child);
        }
        
        if ($child === 'count') {
            $sum += file_get_contents($dirPath .'/'. $child);
        }
    }
    
    return $sum;
}

function summarizeCountsByIterator(string $dirPath): int
{
    if (!is_dir($dirPath)) {
        throw new InvalidArgumentException("$dirPath does not exist or is not a directory.");
    }

    $dirIterator = new RecursiveDirectoryIterator($dirPath, RecursiveDirectoryIterator::SKIP_DOTS);

    $sum = 0;
    foreach ($dirIterator as $file) {
        if ($file->isDir()) {
            $sum += summarizeCountsByIterator($file->getRealPath());
        }
        
        if ($file->isFile() && $file->getFilename() === 'count') {
            $sum += (int) file_get_contents($file->getRealPath());
        }
    }

    return $sum;
}

echo summarizeCountsByIterator(__DIR__ . '/' . $argv[1]);