<?php
/**
 * Form Generator - Creates web forms from DDS field definitions.
 */
namespace App\Generator;

class FormGenerator {
    /**
     * Generate an HTML form from parsed fields.
     */
    public function generate(array $fields, array $options = []): string {
        $method = $options['method'] ?? 'POST';
        $action = $options['action'] ?? '';
        $submitLabel = $options['submit_label'] ?? 'Submit';

        $html = "<form method=\"{$method}\" action=\"{$action}\" class=\"needs-validation\" novalidate>\n";

        foreach ($fields as $field) {
            $html .= $this->renderFormGroup($field);
        }

        $html .= <<<HTML
        <div class="row mt-4">
            <div class="col-sm-9 offset-sm-3">
                <button type="submit" class="btn btn-primary">{$submitLabel}</button>
                <button type="reset" class="btn btn-secondary">Reset</button>
            </div>
        </div>
        </form>
HTML;

        return $html;
    }

    private function renderFormGroup(array $field): string {
        $name = $field['name'];
        $label = $this->toLabel($name);
        $required = $field['mandatory'] ?? false;
        $size = $field['length'];
        $type = $field['data_type'];
        $usage = $field['usage'];

        $reqMark = $required ? ' <span class="text-danger">*</span>' : '';
        $requiredAttr = $required ? ' required' : '';
        $inputClass = 'form-control' . ($required ? ' required-field' : '');
        $readonly = $usage === 'output_only' ? ' readonly' : '';

        $inputType = $type === 'date' ? 'date' : ($type === 'timestamp' ? 'datetime-local' : 'text');
        if ($this->isNumericType($type)) $inputType = 'number';
        if (isset($field['keywords']['PASSWORD'])) $inputType = 'password';

        $html = <<<HTML
        <div class="row mb-3">
            <label for="{$name}" class="col-sm-3 col-form-label">{$label}{$reqMark}</label>
            <div class="col-sm-9">
                <input type="{$inputType}" class="{$inputClass}" id="{$name}" name="{$name}"
                       maxlength="{$size}" {$requiredAttr} {$readonly}
                       placeholder="Enter {$label}">
                <div class="invalid-feedback">Please enter {$label}.</div>
            </div>
        </div>
HTML;

        return $html;
    }

    private function isNumericType(string $type): bool {
        return in_array($type, ['zoned_decimal', 'packed_decimal', 'binary', 'floating_point', 'numeric']);
    }

    private function toLabel(string $name): string {
        return ucwords(str_replace('_', ' ', strtolower($name)));
    }
}
