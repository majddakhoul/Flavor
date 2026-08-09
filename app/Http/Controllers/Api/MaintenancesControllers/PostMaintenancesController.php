<?php

namespace App\Http\Controllers\Api\MaintenancesControllers;

use App\Http\Requests\Api\MaintenancesRequests\PostMaintenancesRequest;
use App\Models\Employee;
use App\Models\Maintenance;

class PostMaintenancesController
{
    public function post_maintenances(PostMaintenancesRequest $postMaintenancesRequest)
    {
        $validatedData = $postMaintenancesRequest->validated();

        $manager = Employee::where('position', 'Manager')->first();

        if (!$manager) {
            $response = [
                'data' => null,
                'success' => false,
                'message' => 'No manager found in the system.',
                'status' => 400
            ];
            return response()->json($response, 400);
        }

        $newMaintenance = [
            'price' => $postMaintenancesRequest->price,
            'maintenance_item' => $postMaintenancesRequest->maintenance_item,
            'notes' => $validatedData['notes'] ?? $postMaintenancesRequest->notes,
            'discount' => $validatedData['discount'] ?? $postMaintenancesRequest->discount,
            'employee_id' => $manager->id,
            'total_price' => $postMaintenancesRequest->price - ($postMaintenancesRequest->price * (($validatedData['discount'] ?? $postMaintenancesRequest->discount) / 100))
        ];

        $maintenance = Maintenance::create($newMaintenance);

        if ($maintenance) {
            $response = [
                'data' => $maintenance,
                'success' => true,
                'message' => 'Maintenance created successfully with manager assignment.',
                'status' => 201
            ];
            return response()->json($response, 201);
        }

        $response = [
            'data' => null,
            'success' => false,
            'message' => 'An error occurred, please try again later.',
            'status' => 400
        ];
        return response()->json($response, 400);
    }
}
