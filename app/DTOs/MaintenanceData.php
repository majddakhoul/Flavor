<?php

namespace App\DTOs;

final class MaintenanceData
{
    public function __construct(
        public readonly string $maintenance_item,
        public readonly int $price,
        public readonly ?int $discount,
        public readonly ?string $notes,
        public readonly int $employee_id,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            maintenance_item: (string) ($data['maintenance_item'] ?? ''),
            price: (int) ($data['price'] ?? 0),
            discount: isset($data['discount']) && $data['discount'] !== '' ? (int) $data['discount'] : null,
            notes: isset($data['notes']) && $data['notes'] !== '' ? (string) $data['notes'] : null,
            employee_id: (int) ($data['employee_id'] ?? 0),
        );
    }

    public function toArray(): array
    {
        return [
            'maintenance_item' => $this->maintenance_item,
            'price' => $this->price,
            'discount' => $this->discount,
            'notes' => $this->notes,
            'employee_id' => $this->employee_id,
        ];
    }
}
