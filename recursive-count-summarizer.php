<?php
/**
 * @author Pavel Lovkii <plovkiy@yandex.ru>
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/seschustle/example-comments-api-client
 */

declare(strict_types=1);

// ANSI цвета
const COLOR_RESET = "\e[0m";
const COLOR_BOLD = "\e[1m";
const COLOR_BLUE = "\e[34m";
const COLOR_GREEN = "\e[32m";
const COLOR_YELLOW = "\e[33m";
const COLOR_RED = "\e[31m";

function summarizeCounts(string $dirPath, int $depth = 0): int
{
    if (!is_dir($dirPath)) {
        echo COLOR_RED . "✗ Error: $dirPath does not exist or is not a directory." . COLOR_RESET . PHP_EOL;
        exit(1);
    }

    $indent = str_repeat('  ', $depth);
    $dirName = basename($dirPath);
    
    if ($depth === 0) {
        echo PHP_EOL . COLOR_BOLD . COLOR_BLUE . "📁 Scanning: " . COLOR_RESET . COLOR_BOLD . $dirPath . COLOR_RESET . PHP_EOL;
        echo str_repeat("─", 70) . PHP_EOL . PHP_EOL;
    } else {
        echo "$indent" . COLOR_YELLOW . "📂 $dirName" . COLOR_RESET . PHP_EOL;
    }

    $dirIterator = new RecursiveDirectoryIterator($dirPath, RecursiveDirectoryIterator::SKIP_DOTS);

    $sum = 0;
    $fileCount = 0;
    
    foreach ($dirIterator as $file) {
        if ($file->isDir()) {
            $sum += summarizeCounts($file->getRealPath(), $depth + 1);
        }
        
        if ($file->isFile() && $file->getFilename() === 'count') {
            $count = (int) file_get_contents($file->getRealPath());
            $fileCount++;
            echo "$indent  " . COLOR_GREEN . "✓ count:" . COLOR_RESET . " $count" . PHP_EOL;
            $sum += $count;
        }
    }

    return $sum;
}

// Проверка аргументов
if (!isset($argv[1])) {
    echo COLOR_RED . "Usage: php recursive-count-summarizer.php <directory>" . COLOR_RESET . PHP_EOL;
    exit(1);
}

$startPath = __DIR__ . '/' . $argv[1];
$total = summarizeCounts($startPath);

echo PHP_EOL . str_repeat("─", 70) . PHP_EOL;
echo COLOR_BOLD . COLOR_GREEN . "📊 Total Count: " . COLOR_RESET . COLOR_BOLD . $total . COLOR_RESET . PHP_EOL;
echo str_repeat("─", 70) . PHP_EOL . PHP_EOL;