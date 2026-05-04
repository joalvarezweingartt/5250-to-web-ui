<?php
/**
 * DDS Field representation.
 */
namespace App\DDS;

class Field {
    public function __construct(
        public readonly string $name,
        public readonly string $dataType,
        public readonly int $length,
        public readonly int $decimals = 0,
        public readonly array $location = ['row' => 0, 'col' => 0],
        public readonly string $usage = 'input_output',
        public readonly array $keywords = [],
    ) {}

    public function isMandatory(): bool {
        return isset($this->keywords['CHECK']) && str_contains((string)$this->keywords['CHECK'], '01');
    }

    public function isNumeric(): bool {
        return in_array($this->dataType, ['zoned_decimal', 'packed_decimal', 'binary', 'floating_point']);
    }

    public function getMaxLength(): int {
        return $this->length;
    }

    public function getValidationRules(): array {
        $rules = [];
        if ($this->isMandatory()) $rules[] = 'required';
        if ($this->isNumeric()) $rules[] = 'numeric';
        if ($this->length > 0 && !$this->isNumeric()) {
            $rules[] = "max:{$this->length}";
        }
        if (isset($this->keywords['RANGE'])) {
            $rules[] = 'range:' . $this->keywords['RANGE'];
        }
        if (isset($this->keywords['VALUES'])) {
            $rules[] = 'in:' . $this->keywords['VALUES'];
        }
        return $rules;
    }

    public function toArray(): array {
        return [
            'name' => $this->name,
            'data_type' => $this->dataType,
            'length' => $this->length,
            'decimals' => $this->decimals,
            'location' => $this->location,
            'usage' => $this->usage,
            'keywords' => $this->keywords,
            'mandatory' => $this->isMandatory(),
            'validation_rules' => $this->getValidationRules(),
        ];
    }
}
