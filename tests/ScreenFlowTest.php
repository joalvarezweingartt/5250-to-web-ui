<?php
/**
 * ScreenFlowTest - Tests for Screen Flow Analyzer
 */
namespace Tests;

use App\Analyzer\ScreenFlow;
use PHPUnit\Framework\TestCase;

class ScreenFlowTest extends TestCase {
    private ScreenFlow $flow;

    protected function setUp(): void {
        $this->flow = new ScreenFlow();
    }

    public function testAddScreen(): void {
        $this->flow->addScreen('MAIN');
        $screens = $this->flow->getScreens();
        $this->assertArrayHasKey('MAIN', $screens);
    }

    public function testAddTransition(): void {
        $this->flow->addScreen('MENU');
        $this->flow->addScreen('INQUIRY');
        $this->flow->addTransition('MENU', 'INQUIRY', 'F10');

        $transitions = $this->flow->getTransitionsFrom('MENU');
        $this->assertCount(1, $transitions);
        $this->assertEquals('INQUIRY', $transitions[0]['to']);
        $this->assertEquals('F10', $transitions[0]['trigger']);
    }

    public function testMultipleTransitions(): void {
        $this->flow->addScreen('MENU');
        $this->flow->addScreen('INQ');
        $this->flow->addScreen('MAINT');
        $this->flow->addTransition('MENU', 'INQ', 'F5');
        $this->flow->addTransition('MENU', 'MAINT', 'F6');

        $transitions = $this->flow->getTransitionsFrom('MENU');
        $this->assertCount(2, $transitions);
    }

    public function testFlowGraph(): void {
        $this->flow->addScreen('A', ['desc' => 'Screen A']);
        $this->flow->addScreen('B', ['desc' => 'Screen B']);
        $this->flow->addTransition('A', 'B', 'F12');

        $graph = $this->flow->getFlowGraph();
        $this->assertArrayHasKey('A', $graph);
        $this->assertArrayHasKey('metadata', $graph['A']);
        $this->assertCount(1, $graph['A']['transitions']);
    }

    public function testToMermaid(): void {
        $this->flow->addScreen('A');
        $this->flow->addScreen('B');
        $this->flow->addTransition('A', 'B', 'F12');

        $mermaid = $this->flow->toMermaid();
        $this->assertStringContainsString('graph TD', $mermaid);
        $this->assertStringContainsString('A -->|F12| B', $mermaid);
    }
}
