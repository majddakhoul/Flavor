<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferRatingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'number_stars' => $this->number_stars,
            'offer_id' => $this->offer_id,
            'customer_id' => $this->customer_id,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
