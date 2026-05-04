<?php
/**
 * FieldTransformerTest
 */
namespace Tests;

use App\Transformer\FieldTransformer;
use PHPUnit\Framework\TestCase;

class FieldTransformerTest extends TestCase {
    private FieldTransformer $transformer;

    protected function setUp(): void {
        $this->transformer = new FieldTransformer();
    }

    public function testTextTransform(): void {
        $field = [
            'name' => 'CUSNO',
            'data_type' => 'alphanumeric',
            'length' => 5,
            'decimals' => 0,
            'usage' => 'both',
            'keywords' => ['CHECK' => '01'],
        ];

        $result = $this->transformer->transform($field);
        $this->assertEquals('CUSNO', $result['name']);
        $this->assertEquals('text', $result['type']);
        $this->assertTrue($result['required']);
        $this->assertEquals(5, $result['maxlength']);
    }

    public function testNumericTransform(): void {
        $field = [
            'name' => 'PRICE',
            'data_type' => 'packed_decimal',
            'length' => 11,
            'decimals' => 2,
            'usage' => 'both',
            'keywords' => [],
        ];

        $result = $this->transformer->transform($field);
        $this->assertEquals('number', $result['type']);
    }

    public function testOutputOnly(): void {
        $field = [
            'name' => 'STATUS',
            'data_type' => 'alphanumeric',
            'length' => 10,
            'decimals' => 0,
            'usage' => 'output_only',
            'keywords' => [],
        ];

        $result = $this->transformer->transform($field);
        $this->assertTrue($result['disabled']);
        $this->assertTrue($result['readonly']);
    }
}
