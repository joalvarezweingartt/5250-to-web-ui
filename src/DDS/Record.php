<?php
/**
 * DDS Record representation.
 */
namespace App\DDS;

class Record {
    public function __construct(
        public readonly string $name,
        public readonly string $type,
        public readonly array $keywords = [],
        public readonly array $fields = [],
    ) {}

    public function isSubfile(): bool {
        return $this->type === 'subfile';
    }

    public function isSubfileControl(): bool {
        return $this->type === 'subfile_control';
    }

    public function isWindow(): bool {
        return $this->type === 'window';
    }

    public function getField(string $name): ?Field {
        foreach ($this->fields as $field) {
            if ($field->name === $name) {
                return $field;
            }
        }
        return null;
    }

    public function getInputFields(): array {
        return array_filter($this->fields, fn(Field $f) => $f->usage !== 'output_only');
    }

    public function getOutputFields(): array {
        return array_filter($this->fields, fn(Field $f) => $f->usage !== 'input_output');
    }
}
