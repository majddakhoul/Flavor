<?php

namespace App\DTOs;

final class ReservationData
{
    public function __construct(
        public readonly int $party_size,
        public readonly ?string $special_requests,
        public readonly array $table_ids,
        public readonly string $start_time,
        public readonly string $end_time,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            party_size: (int) ($data['party_size'] ?? 0),
            special_requests: isset($data['special_requests']) && $data['special_requests'] !== '' ? (string) $data['special_requests'] : null,
            table_ids: (array) ($data['table_ids'] ?? []),
            start_time: (string) ($data['start_time'] ?? ''),
            end_time: (string) ($data['end_time'] ?? ''),
        );
    }

    public function toArray(): array
    {
        return [
            'party_size' => $this->party_size,
            'special_requests' => $this->special_requests,
        ];
    }
}
