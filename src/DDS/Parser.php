<?php
/**
 * 5250 DDS Parser - Parses IBM i Display Data Specification files.
 */
namespace App\DDS;

class Parser {
    private array $records = [];

    /**
     * Parse DDS source content.
     */
    public function parse(string $source): array {
        $lines = explode("\n", $source);
        $currentRecord = null;

        foreach ($lines as $lineNum => $line) {
            $line = rtrim($line);
            if (empty($line) || $this->isComment($line)) continue;

            $record = $this->parseLine($line, $lineNum);
            if ($record) {
                if ($record['type'] === 'record_start') {
                    $currentRecord = $record['name'];
                    $this->records[$currentRecord] = [
                        'name' => $record['name'],
                        'type' => $record['record_type'],
                        'keywords' => $record['keywords'] ?? [],
                        'fields' => [],
                        'line' => $lineNum,
                    ];
                } elseif ($record['type'] === 'field' && $currentRecord) {
                    $this->records[$currentRecord]['fields'][] = $record['field'];
                } elseif ($record['type'] === 'keyword' && $currentRecord) {
                    $this->records[$currentRecord]['keywords'] = array_merge(
                        $this->records[$currentRecord]['keywords'],
                        $record['keywords']
                    );
                }
            }
        }

        return $this->records;
    }

    private function isComment(string $line): bool {
        return str_starts_with(trim($line), '*');
    }

    private function parseLine(string $line, int $lineNum): ?array {
        if (strlen($line) < 7) return null;

        // DDS format: positions 7-16 define record/field name, position 17 defines type
        $name = trim(substr($line, 6, 10));
        $type = $line[16] ?? ' ';

        // Record definitions (position 17 = R)
        if ($type === 'R') {
            $rest = trim(substr($line, 17));
            $keywords = $this->extractKeywords($rest);
            $recordType = $this->detectRecordType($keywords);
            return [
                'type' => 'record_start',
                'name' => $name,
                'record_type' => $recordType,
                'keywords' => $keywords,
            ];
        }

        // Field definitions (position 17 = A/S/B/H/P/etc - data type)
        $dataTypeChars = ['A', 'S', 'B', 'H', 'P', 'F', 'T', 'Z', 'L', 'G'];
        if (in_array($type, $dataTypeChars)) {
            $field = $this->parseField($line, $type, $name);
            return [
                'type' => 'field',
                'field' => $field,
            ];
        }

        // Continuation / keyword lines (position 17 = blank, but has keywords)
        if ($type === ' ' && !empty($name)) {
            $rest = trim(substr($line, 17));
            $keywords = $this->extractKeywords($rest);
            if (!empty($keywords)) {
                return [
                    'type' => 'keyword',
                    'keywords' => $keywords,
                ];
            }
        }

        return null;
    }

    private function parseField(string $line, string $dataType, string $name): array {
        $rest = substr($line, 17);
        $parts = preg_split('/\s+/', trim($rest), 3);

        $length = 0;
        $decimals = 0;
        $location = '';

        if (isset($parts[0])) {
            if (preg_match('/(\d+)(?:[Ss](\d+))?/', $parts[0], $m)) {
                $length = (int) $m[1];
                $decimals = isset($m[2]) ? (int) $m[2] : 0;
            }
        }

        if (isset($parts[1])) {
            $location = $parts[1];
        }

        $keywords = [];
        if (isset($parts[2])) {
            $keywords = $this->extractKeywords($parts[2]);
        }

        return [
            'name' => $name,
            'data_type' => $this->resolveDataType($dataType),
            'length' => $length,
            'decimals' => $decimals,
            'location' => $this->parseLocation($location),
            'usage' => $this->determineUsage($line, $dataType),
            'keywords' => $keywords,
        ];
    }

    private function resolveDataType(string $char): string {
        return match ($char) {
            'A' => 'alphanumeric',
            'S' => 'zoned_decimal',
            'B' => 'binary',
            'H' => 'hexadecimal',
            'P' => 'packed_decimal',
            'F' => 'floating_point',
            'T' => 'timestamp',
            'Z' => 'date',
            'L' => 'logical',
            'G' => 'graphic',
            default => 'unknown',
        };
    }

    private function determineUsage(string $line, string $dataType): string {
        $pos39 = $line[38] ?? ' ';
        return match ($pos39) {
            'B' => 'both',
            'O' => 'output_only',
            'H' => 'hidden',
            default => 'input_output',
        };
    }

    private function parseLocation(string $loc): array {
        if (preg_match('/(\d+)[xX](\d+)/', $loc, $m)) {
            return ['row' => (int) $m[2], 'col' => (int) $m[1]];
        }
        return ['row' => 0, 'col' => 0];
    }

    private function extractKeywords(string $text): array {
        $keywords = [];
        // Match DDS keywords like CHECK(01), EDTCDR(2), CF03(12 'Cancel'), etc.
        preg_match_all('/([A-Z0-9_]+)\(([^)]*)\)/', $text, $matches, PREG_SET_ORDER);
        foreach ($matches as $m) {
            $keywords[$m[1]] = $this->parseKeywordValue($m[2]);
        }
        // Also capture standalone keywords like SFL, SFLCTL, HIGHLIGHT, BLINK
        preg_match_all('/\b(SFL|SFLCTL|HIGHLIGHT|BLINK|REFFLD|MSGID)\b/', $text, $standalone);
        foreach ($standalone[1] as $kw) {
            if (!isset($keywords[$kw])) {
                $keywords[$kw] = true;
            }
        }
        return $keywords;
    }

    private function parseKeywordValue(string $value): mixed {
        $value = trim($value);
        if (is_numeric($value)) {
            return str_contains($value, '.') ? (float) $value : (int) $value;
        }
        // Handle quoted strings like 'Cancel'
        if (preg_match("/^'([^']*)'$/", $value, $m)) {
            return $m[1];
        }
        return $value;
    }

    private function detectRecordType(array $keywords): string {
        if (isset($keywords['SFL'])) return 'subfile';
        if (isset($keywords['SFLCTL'])) return 'subfile_control';
        if (isset($keywords['WINDOW'])) return 'window';
        if (isset($keywords['SELECT'])) return 'menu';
        return 'record';
    }

    public function getRecords(): array {
        return $this->records;
    }
}
