<?php

namespace App\DTOs;

final class IngredientData
{
    public function __construct(
        public readonly string $name,
        public readonly string $unit,
        public readonly int $stock_quantity,
        public readonly int $unit_cost,
        public readonly bool $is_active,
        public readonly array $translations,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: (string) ($data['name'] ?? ''),
            unit: (string) ($data['unit'] ?? ''),
            stock_quantity: (int) ($data['stock_quantity'] ?? 0),
            unit_cost: (int) ($data['unit_cost'] ?? 0),
            is_active: (bool) ($data['is_active'] ?? false),
            translations: (array) ($data['translations'] ?? []),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'unit' => $this->unit,
            'stock_quantity' => $this->stock_quantity,
            'unit_cost' => $this->unit_cost,
            'is_active' => $this->is_active,
        ];
    }
}
