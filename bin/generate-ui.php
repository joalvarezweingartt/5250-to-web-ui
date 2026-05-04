#!/usr/bin/env php
<?php
/**
 * Web UI Generator CLI Tool
 *
 * Generates web UI HTML files from 5250 screen analysis JSON.
 */
require_once __DIR__ . '/../vendor/autoload.php';

use App\Generator\HtmlGenerator;
use App\Generator\FormGenerator;
use App\Generator\SubfileGenerator;
use App\Generator\MenuGenerator;

$options = getopt('', ['input:', 'output:', 'template:', 'help']);

if (isset($options['help']) || empty($options['input'])) {
    echo <<<HELP
Web UI Generator
================
Generates modern web UIs from 5250 screen analysis data.

Usage:
  php bin/generate-ui.php --input analysis.json --output ./web-ui
  php bin/generate-ui.php --input analysis.json --output ./web-ui --template bootstrap5

Options:
  --input <path>     Path to analysis JSON file (from analyze-5250.php)
  --output <dir>     Output directory for generated web files
  --template <name>  UI template: bootstrap5 (default) or minimal
  --help             Show this help message

Examples:
  php bin/generate-ui.php --input analysis.json --output ./public/screens
  php bin/generate-ui.php --input analysis.json --output ./ui --template minimal

HELP;
    exit(1);
}

$inputFile = $options['input'];
$outputDir = rtrim($options['output'] ?? './output', '/');
$template = $options['template'] ?? 'bootstrap5';

if (!file_exists($inputFile)) {
    echo "Error: Input file not found: {$inputFile}\n";
    exit(1);
}

$analysis = json_decode(file_get_contents($inputFile), true);
if ($analysis === null) {
    echo "Error: Invalid JSON in input file.\n";
    exit(1);
}

if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

$htmlGen = new HtmlGenerator(['template' => $template]);
$formGen = new FormGenerator();
$subfileGen = new SubfileGenerator();

foreach ($analysis as $fileName => $data) {
    $screenDir = $outputDir . '/' . pathinfo($fileName, PATHINFO_FILENAME);
    if (!is_dir($screenDir)) {
        mkdir($screenDir, 0755, true);
    }

    foreach ($data['records'] as $record) {
        $screenName = $record['name'];
        echo "Generating: {$screenName}\n";

        $html = $htmlGen->generate(
            ['name' => $screenName, 'command_keys' => []],
            [$record]
        );

        file_put_contents($screenDir . '/' . strtolower($screenName) . '.html', $html);
    }

    // Generate index
    $indexHtml = $htmlGen->generate(
        ['name' => 'Screen Index - ' . pathinfo($fileName, PATHINFO_FILENAME)],
        []
    );
    $indexHtml = str_replace('</body>', '<ul>' . implode('', array_map(function($r) {
        return '<li><a href="' . strtolower($r['name']) . '.html">' . $r['name'] . '</a></li>';
    }, $data['records'])) . '</ul></body>', $indexHtml);
    file_put_contents($outputDir . '/index.html', $indexHtml);
}

echo "UI generation complete. Output: {$outputDir}\n";
