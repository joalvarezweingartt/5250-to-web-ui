<?php
/**
 * Subfile Generator - Creates HTML subfile tables from DDS subfile definitions.
 */
namespace App\Generator;

class SubfileGenerator {
    /**
     * Generate an interactive subfile table.
     */
    public function generate(array $subfileRecord, array $data = [], array $options = []): string {
        $fields = $subfileRecord['fields'] ?? [];
        $pageSize = $subfileRecord['keywords']['SFLPAG'] ?? 15;
        $currentPage = $options['page'] ?? 1;

        $html = '<div class="subfile-wrapper">';
        $html .= $this->renderToolbar($fields, $options);
        $html .= $this->renderTable($fields, $data, $pageSize, $currentPage);
        $html .= $this->renderPagination($pageSize, count($data), $currentPage);
        $html .= $this->renderSelectionBar();
        $html .= '</div>';

        return $html;
    }

    private function renderToolbar(array $fields, array $options): string {
        $html = <<<HTML
        <div class="subfile-toolbar d-flex justify-content-between align-items-center mb-2">
            <div class="btn-group" role="group">
                <button class="btn btn-sm btn-outline-primary" onclick="sflAddRow()">
                    <i class="bi bi-plus"></i> Add
                </button>
                <button class="btn btn-sm btn-outline-danger" onclick="sflDeleteSelected()">
                    <i class="bi bi-trash"></i> Delete
                </button>
                <button class="btn btn-sm btn-outline-secondary" onclick="sflRefresh()">
                    <i class="bi bi-arrow-clockwise"></i> Refresh
                </button>
            </div>
HTML;

        if ($options['search'] ?? true) {
            $html .= <<<HTML
            <div class="search-box">
                <input type="text" class="form-control form-control-sm" placeholder="Search subfile..."
                       onkeyup="sflSearch(this.value)">
            </div>
HTML;
        }

        $html .= '</div>';
        return $html;
    }

    private function renderTable(array $fields, array $data, int $pageSize, int $currentPage): string {
        $html = '<div class="table-responsive"><table class="table table-hover subfile-table mb-0">';
        $html .= '<thead class="table-light"><tr>';
        $html .= '<th class="sfl-select-col" style="width:40px"><input type="checkbox" onclick="sflToggleAll(this)"></th>';
        foreach ($fields as $field) {
            $label = $this->toLabel($field['name'] ?? $field);
            $html .= "<th>{$label}</th>";
        }
        $html .= '<th style="width:80px">Actions</th>';
        $html .= '</tr></thead><tbody>';

        if (empty($data)) {
            $colspan = count($fields) + 2;
            $html .= "<tr><td colspan=\"{$colspan}\" class=\"text-center text-muted py-4\">No records to display.</td></tr>";
        } else {
            $start = ($currentPage - 1) * $pageSize;
            $pageItems = array_slice($data, $start, $pageSize);
            foreach ($pageItems as $index => $row) {
                $html .= $this->renderRow($fields, $row, $index);
            }
        }

        $html .= '</tbody></table></div>';
        return $html;
    }

    private function renderRow(array $fields, array $row, int $index): string {
        $html = "<tr onclick=\"sflSelectRow(this)\">";
        $html .= "<td><input type=\"checkbox\" class=\"sfl-select\" value=\"{$index}\"></td>";
        foreach ($fields as $field) {
            $name = $field['name'] ?? $field;
            $value = $row[$name] ?? '';
            $html .= "<td>" . htmlspecialchars((string)$value) . "</td>";
        }
        $html .= '<td class="text-nowrap">
                    <button class="btn btn-sm btn-outline-primary py-0 px-1" onclick="sflEdit(\'' . $index . '\')">E</button>
                    <button class="btn btn-sm btn-outline-danger py-0 px-1" onclick="sflDelete(\'' . $index . '\')">D</button>
                 </td>';
        $html .= '</tr>';
        return $html;
    }

    private function renderPagination(int $pageSize, int $total, int $currentPage): string {
        $totalPages = max(1, (int)ceil($total / $pageSize));
        $html = '<nav class="subfile-pagination"><ul class="pagination pagination-sm justify-content-center mb-0 mt-2">';

        $prevDisabled = $currentPage <= 1 ? ' disabled' : '';
        $html .= "<li class=\"page-item{$prevDisabled}\"><a class=\"page-link\" href=\"#\" onclick=\"sflPage(" . ($currentPage - 1) . ")\">&laquo;</a></li>";

        for ($p = 1; $p <= $totalPages; $p++) {
            $active = $p === $currentPage ? ' active' : '';
            $html .= "<li class=\"page-item{$active}\"><a class=\"page-link\" href=\"#\" onclick=\"sflPage({$p})\">{$p}</a></li>";
        }

        $nextDisabled = $currentPage >= $totalPages ? ' disabled' : '';
        $html .= "<li class=\"page-item{$nextDisabled}\"><a class=\"page-link\" href=\"#\" onclick=\"sflPage(" . ($currentPage + 1) . ")\">&raquo;</a></li>";
        $html .= '</ul></nav>';

        return $html;
    }

    private function renderSelectionBar(): string {
        return <<<HTML
        <div class="subfile-status text-muted small mt-1 d-flex justify-content-between">
            <span id="sflSelectedCount">0 selected</span>
            <span id="sflTotalCount"></span>
        </div>
HTML;
    }

    private function toLabel(string $name): string {
        return ucwords(str_replace('_', ' ', strtolower($name)));
    }
}
