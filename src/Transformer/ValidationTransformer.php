<?php
/**
 * Validation Transformer - Converts DDS validation rules to JavaScript/HTML5 validation.
 */
namespace App\Transformer;

class ValidationTransformer {
    /**
     * Transform DDS validation rules into an object with HTML5 and JS validators.
     */
    public function transform(array $field): array {
        return [
            'html5' => $this->getHtml5Validation($field),
            'javascript' => $this->getJavaScriptValidation($field),
            'messages' => $this->getValidationMessages($field),
        ];
    }

    private function getHtml5Validation(array $field): array {
        $rules = [];
        $keywords = $field['keywords'] ?? [];

        // Required (CHECK(01))
        $check = $keywords['CHECK'] ?? '';
        if (str_contains((string)$check, '01')) {
            $rules['required'] = true;
        }

        // Max length
        if ($field['length'] > 0) {
            $rules['maxlength'] = $field['length'];
        }

        // Range
        if (isset($keywords['RANGE'])) {
            $range = $keywords['RANGE'];
            if (is_string($range)) {
                $parts = explode(' ', $range);
                if (isset($parts[0])) $rules['min'] = (float) $parts[0];
                if (isset($parts[1])) $rules['max'] = (float) $parts[1];
            }
        }

        return $rules;
    }

    private function getJavaScriptValidation(array $field): array {
        $validators = [];
        $keywords = $field['keywords'] ?? [];

        // Comp (compare with another field)
        if (isset($keywords['COMP'])) {
            $validators[] = [
                'type' => 'compare',
                'field' => $keywords['COMP'],
                'message' => "Must match {$keywords['COMP']}",
            ];
        }

        // Values (list of valid values)
        if (isset($keywords['VALUES'])) {
            $valueStr = (string) $keywords['VALUES'];
            $values = preg_split('/\s+/', $valueStr);
            $validators[] = [
                'type' => 'in',
                'values' => $values,
                'message' => 'Invalid value selected',
            ];
        }

        // Numeric check (CHECK(02))
        $check = $keywords['CHECK'] ?? '';
        if (str_contains((string)$check, '02')) {
            $validators[] = [
                'type' => 'numeric',
                'message' => 'Must be a number',
            ];
        }

        // Alpha check (CHECK(04))
        if (str_contains((string)$check, '04')) {
            $validators[] = [
                'type' => 'alpha',
                'message' => 'Must contain letters only',
            ];
        }

        return $validators;
    }

    private function getValidationMessages(array $field): array {
        $keywords = $field['keywords'] ?? [];
        $messages = [];

        if (isset($keywords['ERRMSG'])) {
            $messages['error'] = $keywords['ERRMSG'];
        }

        if (isset($keywords['MSGID'])) {
            $messages['msgid'] = $keywords['MSGID'];
        }

        return $messages;
    }

    /**
     * Generate JavaScript validation code for a field.
     */
    public function generateJavaScript(array $field): string {
        $name = $field['name'];
        $validators = $this->getJavaScriptValidation($field);
        if (empty($validators)) return '';

        $js = "function validate_{$name}(value) {\n";
        foreach ($validators as $v) {
            switch ($v['type']) {
                case 'required':
                    $js .= "    if (!value || value.trim() === '') return '{$v['message']}';\n";
                    break;
                case 'numeric':
                    $js .= "    if (isNaN(value)) return '{$v['message']}';\n";
                    break;
                case 'alpha':
                    $js .= "    if (!/^[A-Za-z]+$/.test(value)) return '{$v['message']}';\n";
                    break;
            }
        }
        $js .= "    return null;\n";
        $js .= "}\n";
        return $js;
    }
}
