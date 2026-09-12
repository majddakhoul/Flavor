<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'country' => $this->country,
            'state' => $this->state,
            'city' => $this->city,
            'region' => $this->region,
            'street' => $this->street,
            'label' => $this->label,
            'delivery_time' => $this->delivery_time,
            'estimated_delivery' => $this->estimated_delivery,
        ];
    }
}
