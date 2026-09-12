<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'initials' => $this->initials,
            'email' => $this->email,
            'phone' => $this->phone,
            'gender' => $this->gender?->value,
            'status' => $this->status,
            'user_type' => $this->user_type->value,
            'email_verified' => $this->hasVerifiedEmail(),
            'preferred_locale' => $this->preferred_locale,
            'location' => new LocationResource($this->whenLoaded('location')),
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'employee' => new EmployeeResource($this->whenLoaded('employee')),
        ];
    }
}
