<?php
/**
 * File Loader - Loads and validates file contents.
 */
namespace App\Utils;

class FileLoader {
    /**
     * Load file contents, supporting various encodings.
     */
    public static function load(string $path): string {
        if (!file_exists($path)) {
            throw new \RuntimeException("File not found: {$path}");
        }

        $content = file_get_contents($path);
        if ($content === false) {
            throw new \RuntimeException("Unable to read file: {$path}");
        }

        // Handle IBM i EBCDIC conversion if needed
        $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'EBCDIC-CP-US'], true);
        if ($encoding === 'EBCDIC-CP-US') {
            $content = mb_convert_encoding($content, 'UTF-8', 'EBCDIC-CP-US');
        }

        return $content;
    }

    /**
     * Find DDS files in a directory.
     */
    public static function findDdsFiles(string $directory): array {
        $files = glob($directory . '/*.dds', GLOB_BRACE) ?: [];
        $files = array_merge($files, glob($directory . '/*.DDs', GLOB_BRACE) ?: []);
        $files = array_merge($files, glob($directory . '/*.DDS', GLOB_BRACE) ?: []);
        $files = array_merge($files, glob($directory . '/*.dspf', GLOB_BRACE) ?: []);
        $files = array_merge($files, glob($directory . '/*.DSPF', GLOB_BRACE) ?: []);

        sort($files);
        return $files;
    }

    /**
     * Get a human-readable file size.
     */
    public static function formatSize(int $bytes): string {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 1) . ' ' . $units[$i];
    }
}
