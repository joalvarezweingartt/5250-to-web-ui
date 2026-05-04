<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Neuraldev\ScreenToWeb\Service\ScreenMapper;

class ScreenMapperTest extends TestCase {
    private ScreenMapper $mapper;
    
    protected function setUp(): void {
        $this->mapper = new ScreenMapper();
    }
    
    public function testMapsSubfileData(): void {
        $input = "ACME Corp|1001|1250.50~Beta Inc|1002|3400.00~";
        $result = $this->mapper->mapSubfileData($input);
        
        $this->assertCount(2, $result);
        $this->assertEquals('ACME Corp', $result[0]['name']);
        $this->assertEquals(1001, $result[0]['id']);
        $this->assertEquals(1250.50, $result[0]['balance']);
    }
    
    public function testBuildsSubfileResponse(): void {
        $records = [['name' => 'A', 'id' => 1, 'balance' => 100]];
        $result = $this->mapper->buildSubfileResponse($records, 1, 10);
        
        $this->assertEquals(1, $result['total']);
        $this->assertEquals(1, $result['totalPages']);
    }
}
