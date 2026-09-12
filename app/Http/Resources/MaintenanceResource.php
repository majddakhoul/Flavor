<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MaintenanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'maintenance_item' => $this->maintenance_item,
            'price' => $this->price,
            'discount' => $this->discount,
            'total_price' => $this->total_price,
            'saved_amount' => $this->saved_amount,
            'notes' => $this->notes,
            'created_at' => $this->created_at?->toIso8601String(),
            'employee' => new EmployeeResource($this->whenLoaded('employee')),
        ];
    }
}
