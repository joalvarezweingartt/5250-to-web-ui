<?php
/**
 * Screen Flow Analyzer - Maps navigation between 5250 screens.
 */
namespace App\Analyzer;

class ScreenFlow {
    private array $screens = [];
    private array $transitions = [];
    private array $commandKeys = [];

    /**
     * Add a screen to the flow graph.
     */
    public function addScreen(string $name, array $metadata = []): void {
        $this->screens[$name] = $metadata;
    }

    /**
     * Define a transition between screens.
     */
    public function addTransition(string $from, string $to, string $trigger): void {
        $this->transitions[] = [
            'from' => $from,
            'to' => $to,
            'trigger' => $trigger,
        ];
        if (!in_array($trigger, $this->commandKeys)) {
            $this->commandKeys[] = $trigger;
        }
    }

    /**
     * Get all screens.
     */
    public function getScreens(): array {
        return $this->screens;
    }

    /**
     * Get transitions from a specific screen.
     */
    public function getTransitionsFrom(string $screen): array {
        return array_filter($this->transitions, fn($t) => $t['from'] === $screen);
    }

    /**
     * Get the full navigation graph.
     */
    public function getFlowGraph(): array {
        $graph = [];
        foreach ($this->screens as $name => $meta) {
            $graph[$name] = [
                'metadata' => $meta,
                'transitions' => array_values($this->getTransitionsFrom($name)),
            ];
        }
        return $graph;
    }

    /**
     * Detect cycles in the navigation graph.
     */
    public function detectCycles(): array {
        $visited = [];
        $stack = [];
        $cycles = [];

        foreach (array_keys($this->screens) as $screen) {
            if (!isset($visited[$screen])) {
                $this->detectCyclesFrom($screen, $visited, $stack, $cycles);
            }
        }

        return $cycles;
    }

    private function detectCyclesFrom(string $node, array &$visited, array &$stack, array &$cycles): void {
        $visited[$node] = true;
        $stack[$node] = true;

        foreach ($this->getTransitionsFrom($node) as $transition) {
            $to = $transition['to'];
            if (!isset($visited[$to])) {
                $this->detectCyclesFrom($to, $visited, $stack, $cycles);
            } elseif (isset($stack[$to])) {
                $cycles[] = "Cycle detected: {$node} -> {$to}";
            }
        }

        unset($stack[$node]);
    }

    /**
     * Generate a Mermaid.js diagram of the screen flow.
     */
    public function toMermaid(): string {
        $lines = ["graph TD;"];
        foreach ($this->transitions as $t) {
            $label = $t['trigger'];
            $lines[] = "    {$t['from']} -->|{$label}| {$t['to']}";
        }
        return implode("\n", $lines);
    }
}
