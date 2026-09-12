<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'notes' => $this->notes,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'order_type' => $this->order_type->value,
            'order_type_label' => $this->order_type->label(),
            'dated_at' => $this->dated_at?->toDateString(),
            'meals_total' => $this->meals_total,
            'offers_total' => $this->offers_total,
            'tables_total' => $this->tables_total,
            'total' => $this->total,
            'items_count' => $this->items_count,
            'estimated_delivery' => $this->estimated_delivery,
            'created_at' => $this->created_at?->toIso8601String(),
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'employee' => new EmployeeResource($this->whenLoaded('employee')),
            'location' => new LocationResource($this->whenLoaded('location')),
            'reservation' => new ReservationResource($this->whenLoaded('reservation')),
            'meals' => MealResource::collection($this->whenLoaded('meals')),
            'offers' => OfferResource::collection($this->whenLoaded('offers')),
        ];
    }
}
