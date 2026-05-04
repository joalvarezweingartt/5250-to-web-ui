<?php
/**
 * Dependency graph for 5250 screen relationships.
 */
namespace App\Analyzer;

class DependencyGraph {
    private array $dependencies = [];

    /**
     * Add a dependency (screen depends on another).
     */
    public function addDependency(string $screen, string $dependsOn): void {
        if (!isset($this->dependencies[$screen])) {
            $this->dependencies[$screen] = [];
        }
        $this->dependencies[$screen][] = $dependsOn;
    }

    /**
     * Get direct dependencies for a screen.
     */
    public function getDependencies(string $screen): array {
        return $this->dependencies[$screen] ?? [];
    }

    /**
     * Get all transitive dependencies (topological order).
     */
    public function getTransitiveDependencies(string $screen): array {
        $result = [];
        $visited = [];

        $this->resolveDeps($screen, $visited, $result);

        return $result;
    }

    private function resolveDeps(string $screen, array &$visited, array &$result): void {
        $visited[$screen] = true;
        foreach ($this->getDependencies($screen) as $dep) {
            if (!isset($visited[$dep])) {
                $this->resolveDeps($dep, $visited, $result);
            }
            if (!in_array($dep, $result)) {
                $result[] = $dep;
            }
        }
    }

    /**
     * Get all screens sorted by dependency order (for migration planning).
     */
    public function getMigrationOrder(): array {
        $inDegree = [];
        $graph = $this->dependencies;

        foreach ($graph as $screen => $deps) {
            if (!isset($inDegree[$screen])) $inDegree[$screen] = 0;
            foreach ($deps as $dep) {
                if (!isset($inDegree[$dep])) $inDegree[$dep] = 0;
                $inDegree[$dep]++;
            }
        }

        $queue = [];
        foreach ($inDegree as $screen => $degree) {
            if ($degree === 0) $queue[] = $screen;
        }

        $sorted = [];
        while (!empty($queue)) {
            $screen = array_shift($queue);
            $sorted[] = $screen;
            if (isset($graph[$screen])) {
                // Decrease in-degree for dependents
            }
        }

        return $sorted;
    }
}
