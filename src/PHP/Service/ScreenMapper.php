<?php
declare(strict_types=1);

namespace Neuraldev\ScreenToWeb\Service;

class ScreenMapper {
    public function mapSubfileData(string $rpgOutput): array {
        $records = [];
        $lines = explode('~', $rpgOutput);
        
        foreach ($lines as $line) {
            if (empty(trim($line))) continue;
            $parts = explode('|', $line);
            if (count($parts) >= 3) {
                $records[] = [
                    'name' => $parts[0],
                    'id' => (int) $parts[1],
                    'balance' => (float) $parts[2],
                ];
            }
        }
        
        return $records;
    }
    
    public function buildSubfileResponse(array $records, int $page, int $pageSize): array {
        $total = count($records);
        $offset = ($page - 1) * $pageSize;
        $paged = array_slice($records, $offset, $pageSize);
        
        return [
            'data' => $paged,
            'page' => $page,
            'pageSize' => $pageSize,
            'total' => $total,
            'totalPages' => (int) ceil($total / $pageSize),
        ];
    }
}
