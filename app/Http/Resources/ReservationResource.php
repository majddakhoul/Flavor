<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reservation_code' => $this->reservation_code,
            'party_size' => $this->party_size,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'type' => $this->type->value,
            'date' => $this->date?->toDateString(),
            'special_requests' => $this->special_requests,
            'starts_at' => $this->starts_at?->toIso8601String(),
            'ends_at' => $this->ends_at?->toIso8601String(),
            'duration_hours' => $this->duration_hours,
            'seats_booked' => $this->seats_booked,
            'tables_cost' => $this->tables_cost,
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'employee' => new EmployeeResource($this->whenLoaded('employee')),
            'tables' => TableResource::collection($this->whenLoaded('tables')),
        ];
    }
}
