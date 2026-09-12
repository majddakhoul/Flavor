<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TableResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'table_number' => $this->table_number,
            'label' => $this->label,
            'capacity' => $this->capacity,
            'location' => $this->location->value,
            'location_label' => $this->location->label(),
            'is_active' => $this->is_active,
            'price_per_hour' => $this->price_per_hour,
            'description' => $this->t('description'),
        ];
    }
}
