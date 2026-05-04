#!/usr/bin/env php
<?php
/**
 * 5250 DDS Analyzer CLI Tool
 *
 * Parses and analyzes IBM i 5250 display file (DDS) source files,
 * outputting structured metadata for web UI generation.
 */
require_once __DIR__ . '/../vendor/autoload.php';

use App\DDS\Parser;
use App\Utils\FileLoader;

$options = getopt('', ['file:', 'dir:', 'output:', 'format:', 'help']);
$showHelp = isset($options['help']);

if ($showHelp || (empty($options['file']) && empty($options['dir']))) {
    echo <<<HELP
5250-to-Web-UI Analyzer
========================
Analyzes IBM i 5250 DDS display files and outputs structured metadata.

Usage:
  php bin/analyze-5250.php --file <path>          Analyze a single DDS file
  php bin/analyze-5250.php --dir <directory>      Analyze all DDS files in a directory
  php bin/analyze-5250.php --file <path> --output result.json   Save output to file

Options:
  --file <path>      Path to a DDS source file
  --dir <directory>  Directory containing DDS files
  --output <path>    Save analysis as JSON (optional)
  --format <fmt>     Output format: json (default) or table
  --help             Show this help message

Examples:
  php bin/analyze-5250.php --file examples/order-entry.dds
  php bin/analyze-5250.php --dir examples/ --output analysis.json

HELP;
    exit($showHelp ? 0 : 1);
}

$parser = new Parser();
$results = [];

if (!empty($options['file'])) {
    $filePath = $options['file'];
    echo "Analyzing: {$filePath}\n";
    $content = FileLoader::load($filePath);
    $records = $parser->parse($content);
    $results[basename($filePath)] = [
        'file' => $filePath,
        'records' => $records,
        'record_count' => count($records),
    ];
}

if (!empty($options['dir'])) {
    $dir = rtrim($options['dir'], '/');
    $files = FileLoader::findDdsFiles($dir);
    foreach ($files as $file) {
        echo "Analyzing: {$file}\n";
        $content = FileLoader::load($file);
        $records = $parser->parse($content);
        $results[basename($file)] = [
            'file' => $file,
            'records' => $records,
            'record_count' => count($records),
        ];
    }
}

$format = $options['format'] ?? 'json';

if ($format === 'table') {
    foreach ($results as $name => $data) {
        echo "\n=== {$name} ===\n";
        echo "Records: {$data['record_count']}\n\n";
        foreach ($data['records'] as $record) {
            echo "  [{$record['type']}] {$record['name']}\n";
            echo "    Keywords: " . implode(', ', array_keys($record['keywords'])) . "\n";
            echo "    Fields: " . count($record['fields']) . "\n";
        }
    }
} else {
    $json = json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    if (!empty($options['output'])) {
        file_put_contents($options['output'], $json);
        echo "Output saved to: {$options['output']}\n";
    } else {
        echo $json;
    }
}

echo "\nDone.\n";
