<?php

namespace App\Http\Controllers\Api\MaintenancesControllers;

use App\Http\Requests\Api\MaintenancesRequests\UpdateOneMaintenancesRequest;
use App\Models\Maintenance;

class UpdateOneMaintenancesController
{

    public function update_one_maintenances(UpdateOneMaintenancesRequest $updateOneMaintenancesRequest)
    {

        $ValidatedData = $updateOneMaintenancesRequest->validated();

        $Maintenances = Maintenance::find($updateOneMaintenancesRequest->maintenance_id);

        if (!$Maintenances) {
            $Response['data'] = null;
            $Response['success'] = false;
            $Response['message'] = 'No Maintenances found with the specified attributes.';
            $Response['status'] = 404;

            return response()->json($Response, 404);
        }

        $UpdateData = [
            'price' => $ValidatedData['price'] ?? $Maintenances->price,
            'maintenance_item' => $ValidatedData['maintenance_item'] ?? $Maintenances->maintenance_item,
            'notes' => $ValidatedData['notes'] ?? $Maintenances->notes,
            'discount' => $ValidatedData['discount'] ?? $Maintenances->discount,
        ];

        $Updated = $Maintenances->update($UpdateData);

        $Maintenances->total_price = $Maintenances->price - ($Maintenances->price * ($Maintenances->discount / 100));
        $Maintenances->update();

        if ($Updated) {
            $Response['data'] = $Maintenances->fresh();
            $Response['success'] = true;
            $Response['message'] = 'Maintenances information updated successfully.';
            $Response['status'] = 200;
            return response()->json($Response, 200);
        } else {
            $Response['data'] = null;
            $Response['success'] = false;
            $Response['message'] = 'Failed to update your Maintenances information.';
            $Response['status'] = 400;

            return response()->json($Response, 400);
        }
    }
}
