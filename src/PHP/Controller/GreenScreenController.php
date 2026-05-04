<?php
declare(strict_types=1);

namespace Neuraldev\ScreenToWeb\Controller;

use Neuraldev\ScreenToWeb\Service\ScreenMapper;

class GreenScreenController {
    private ScreenMapper $mapper;
    
    public function __construct() {
        $this->mapper = new ScreenMapper();
    }
    
    public function inquiry(array $params): array {
        $searchKey = $params['search'] ?? '';
        $page = (int) ($params['page'] ?? 1);
        
        // In production, this would call RPG via XMLSERVICE
        $rpgOutput = "ACME Corp|1001|1250.50~Beta Inc|1002|3400.00~Gamma LLC|1003|780.25~";
        
        $records = $this->mapper->mapSubfileData($rpgOutput);
        return $this->mapper->buildSubfileResponse($records, $page, 10);
    }
    
    public function orderStatus(string $orderId): array {
        // Simulate RPG call
        return [
            'orderId' => $orderId,
            'status' => 'SHIPPED',
            'customer' => 'ACME Corp',
            'total' => 2499.99,
            'items' => 3,
        ];
    }
}
