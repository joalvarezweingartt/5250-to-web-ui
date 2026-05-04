<?php
/**
 * Field Transformer - Transforms 5250 field definitions to web form fields.
 */
namespace App\Transformer;

class FieldTransformer {
    /**
     * Transform a DDS field to a web form field definition.
     */
    public function transform(array $ddsField): array {
        return [
            'name' => $ddsField['name'],
            'type' => $this->getHtmlType($ddsField),
            'label' => $this->toLabel($ddsField['name']),
            'placeholder' => $this->toLabel($ddsField['name']),
            'required' => $this->isRequired($ddsField),
            'disabled' => $ddsField['usage'] === 'output_only',
            'readonly' => $ddsField['usage'] === 'output_only',
            'maxlength' => $ddsField['length'],
            'pattern' => $this->getPattern($ddsField),
            'validation' => $this->getValidationRules($ddsField),
            'attributes' => $this->getAdditionalAttributes($ddsField),
        ];
    }

    private function getHtmlType(array $field): string {
        $keywords = $field['keywords'] ?? [];
        if (isset($keywords['PASSWORD'])) return 'password';
        if ($field['data_type'] === 'date') return 'date';
        if ($field['data_type'] === 'timestamp') return 'datetime-local';
        if ($this->isNumeric($field['data_type'])) return 'number';
        return 'text';
    }

    private function isRequired(array $field): bool {
        $check = $field['keywords']['CHECK'] ?? '';
        return str_contains((string)$check, '01');
    }

    private function isNumeric(string $type): bool {
        return in_array($type, ['zoned_decimal', 'packed_decimal', 'binary', 'floating_point']);
    }

    private function getPattern(array $field): ?string {
        $check = $field['keywords']['CHECK'] ?? '';
        if (str_contains((string)$check, '04')) return '[A-Za-z]*';
        if (str_contains((string)$check, '02')) return '[0-9]*';
        return null;
    }

    private function getValidationRules(array $field): array {
        $rules = [];
        if ($this->isRequired($field)) $rules[] = 'required';
        if ($this->isNumeric($field['data_type'])) $rules[] = 'numeric';
        $rules[] = "max:{$field['length']}";
        return $rules;
    }

    private function getAdditionalAttributes(array $field): array {
        $attrs = [];
        if ($field['decimals'] > 0) {
            $attrs['step'] = pow(10, -$field['decimals']);
        }
        if (!empty($field['keywords']['VALUES'])) {
            $attrs['data-values'] = $field['keywords']['VALUES'];
        }
        return $attrs;
    }

    private function toLabel(string $name): string {
        return ucwords(str_replace('_', ' ', strtolower($name)));
    }
}
