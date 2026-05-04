<?php
/**
 * ParserTest - Tests for DDS Parser
 */
namespace Tests;

use App\DDS\Parser;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase {
    private Parser $parser;

    protected function setUp(): void {
        $this->parser = new Parser();
    }

    public function testParseSimpleRecord(): void {
        $dds = "A          R CUSTOMER                   WINDOW(10 20)";
        $result = $this->parser->parse($dds);
        
        $this->assertArrayHasKey('CUSTOMER', $result);
        $this->assertEquals('window', $result['CUSTOMER']['type']);
    }

    public function testParseField(): void {
        $dds = "A            CUSNO         5A  B  7  2CHECK(01)";
        $result = $this->parser->parse($dds);

        $record = reset($result);
        $this->assertNotEmpty($record['fields']);
        
        $field = $record['fields'][0];
        $this->assertEquals('CUSNO', $field['name']);
        $this->assertEquals('alphanumeric', $field['data_type']);
        $this->assertEquals(5, $field['length']);
    }

    public function testParseSubfile(): void {
        $dds = "A          R CUSTOMERL                  SFL";
        $result = $this->parser->parse($dds);

        $this->assertArrayHasKey('CUSTOMERL', $result);
        $this->assertEquals('subfile', $result['CUSTOMERL']['type']);
    }

    public function testParseSubfileControl(): void {
        $dds = "A          R CUSTCTL                    SFLCTL(CUSTOMERL)";
        $result = $this->parser->parse($dds);

        $this->assertArrayHasKey('CUSTCTL', $result);
        $this->assertEquals('subfile_control', $result['CUSTCTL']['type']);
    }

    public function testParseKeywords(): void {
        $dds = "A            PRICE        11S 2B  9  5RANGE(0 99999.99)";
        $result = $this->parser->parse($dds);

        $record = reset($result);
        $field = $record['fields'][0];
        $this->assertArrayHasKey('RANGE', $field['keywords']);
    }

    public function testParseNumericField(): void {
        $dds = "A            BALANCE       7S 2O  2 40EDTCDR(2)";
        $result = $this->parser->parse($dds);

        $record = reset($result);
        $field = $record['fields'][0];
        $this->assertEquals('zoned_decimal', $field['data_type']);
        $this->assertEquals(7, $field['length']);
        $this->assertEquals(2, $field['decimals']);
    }

    public function testParseCommandKeys(): void {
        $dds = "A  23                                   CF03(12 'Cancel')";
        $result = $this->parser->parse($dds);

        $this->assertNotEmpty($result);
    }

    public function testParseWithComments(): void {
        $dds = "* This is a comment\nA          R TESTRCD";
        $result = $this->parser->parse($dds);

        $this->assertArrayHasKey('TESTRCD', $result);
    }

    public function testParseMultipleRecords(): void {
        $dds = "A          R HEADER\nA          R DETAIL\nA          R FOOTER";
        $result = $this->parser->parse($dds);

        $this->assertCount(3, $result);
        $this->assertArrayHasKey('HEADER', $result);
        $this->assertArrayHasKey('DETAIL', $result);
        $this->assertArrayHasKey('FOOTER', $result);
    }
}
