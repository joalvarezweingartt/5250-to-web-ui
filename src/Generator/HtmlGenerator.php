<?php
/**
 * HTML Generator - Generates web UI from parsed screen definitions.
 */
namespace App\Generator;

class HtmlGenerator {
    private array $config;

    public function __construct(array $config = []) {
        $this->config = array_merge([
            'template' => 'bootstrap5',
            'responsive' => true,
            'include_search' => true,
            'include_navigation' => true,
        ], $config);
    }

    /**
     * Generate a complete HTML page from a screen definition.
     */
    public function generate(array $screen, array $records): string {
        $title = $screen['name'] ?? 'Screen';
        $html = $this->renderHeader($title);
        $html .= $this->renderNavigation($screen);
        $html .= $this->renderContent($records);
        $html .= $this->renderCommandKeys($screen['command_keys'] ?? []);
        $html .= $this->renderFooter();
        return $html;
    }

    private function renderHeader(string $title): string {
        $template = $this->config['template'];
        if ($template === 'bootstrap5') {
            return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f5f5f5; padding-top: 60px; }
        .screen-container { max-width: 1200px; margin: 0 auto; }
        .command-bar { position: fixed; bottom: 0; left: 0; right: 0; background: #fff; border-top: 2px solid #dee2e6; padding: 8px 16px; }
        .command-bar .btn { margin-right: 4px; }
        .field-label { font-weight: 600; color: #495057; }
        .subfile-table { font-size: 0.9rem; }
        .subfile-table tr:hover { background: #f0f7ff; cursor: pointer; }
        .screen-title { border-bottom: 2px solid #0d6efd; padding-bottom: 8px; margin-bottom: 20px; }
        @media print { .command-bar { display: none; } }
    </style>
</head>
<body>
    <div class="screen-container container">
HTML;
        }
        return "<!DOCTYPE html><html><head><title>{$title}</title></head><body>\n";
    }

    private function renderNavigation(array $screen): string {
        return <<<HTML
        <div class="d-flex justify-content-between align-items-center screen-title">
            <h3>{$screen['name']}</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">{$screen['name']}</li>
                </ol>
            </nav>
        </div>
HTML;
    }

    private function renderContent(array $records): string {
        $html = '';
        foreach ($records as $record) {
            $html .= $this->renderRecord($record);
        }
        return $html;
    }

    private function renderRecord(array $record): string {
        $type = $record['type'] ?? 'record';
        return match ($type) {
            'subfile' => $this->renderSubfile($record),
            'subfile_control' => $this->renderSubfileControl($record),
            'window' => $this->renderWindow($record),
            default => $this->renderFormRecord($record),
        };
    }

    private function renderFormRecord(array $record): string {
        $html = '<div class="card mb-4"><div class="card-body"><form>';
        foreach ($record['fields'] as $field) {
            $html .= $this->renderField($field);
        }
        $html .= '</form></div></div>';
        return $html;
    }

    private function renderField(array $field): string {
        $name = $field['name'];
        $label = $this->toLabel($name);
        $required = $field['mandatory'] ?? false;
        $req = $required ? ' <span class="text-danger">*</span>' : '';

        $html = <<<HTML
        <div class="row mb-3">
            <label class="col-sm-3 col-form-label field-label">{$label}{$req}</label>
            <div class="col-sm-9">
HTML;

        if ($field['usage'] === 'output_only') {
            $html .= <<<HTML
                <p class="form-control-plaintext" id="{$name}">[{$name}]</p>
HTML;
        } else {
            $html .= <<<HTML
                <input type="text" class="form-control" id="{$name}" name="{$name}"
                       maxlength="{$field['length']}" placeholder="{$label}">
HTML;
        }

        $html .= '</div></div>';
        return $html;
    }

    private function renderSubfile(array $record): string {
        $fields = $record['fields'];
        $html = '<div class="card mb-4"><div class="card-header d-flex justify-content-between"><span>Subfile</span>';
        $html .= '<div class="btn-group btn-group-sm"><button class="btn btn-outline-secondary">&laquo;</button>';
        $html .= '<button class="btn btn-outline-secondary">Page 1</button>';
        $html .= '<button class="btn btn-outline-secondary">&raquo;</button></div></div>';
        $html .= '<div class="card-body p-0"><div class="table-responsive"><table class="table table-striped table-hover subfile-table mb-0">';
        $html .= '<thead class="table-light"><tr>';
        foreach ($fields as $field) {
            $html .= '<th>' . $this->toLabel($field['name']) . '</th>';
        }
        $html .= '<th>Actions</th></tr></thead><tbody>';
        $html .= '<tr><td colspan="' . (count($fields) + 1) . '" class="text-center text-muted py-4">No records to display</td></tr>';
        $html .= '</tbody></table></div></div></div>';
        return $html;
    }

    private function renderSubfileControl(array $record): string {
        return ''; // Control is handled implicitly with the subfile
    }

    private function renderWindow(array $record): string {
        return $this->renderFormRecord($record);
    }

    private function renderCommandKeys(array $keys): string {
        if (empty($keys)) {
            $keys = ['F3', 'F5', 'F6', 'F12'];
        }

        $html = <<<HTML
    </div>
    <div class="command-bar">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="btn-group" role="group">
HTML;
        foreach ($keys as $key => $label) {
            $keyLabel = is_string($label) ? $label : "F{$key}";
            $html .= <<<HTML
                <button class="btn btn-outline-primary btn-sm command-key" data-key="{$keyLabel}">{$keyLabel}</button>
HTML;
        }
        $html .= <<<HTML
            </div>
            <small class="text-muted">5250 to Web UI</small>
        </div>
    </div>
HTML;
        return $html;
    }

    private function renderFooter(): string {
        return '</body></html>';
    }

    private function toLabel(string $name): string {
        return ucwords(str_replace('_', ' ', strtolower($name)));
    }
}
