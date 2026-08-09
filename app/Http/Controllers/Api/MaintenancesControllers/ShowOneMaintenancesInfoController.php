<?php

namespace App\Http\Controllers\Api\MaintenancesControllers;

use App\Models\Maintenance;

class ShowOneMaintenancesInfoController
{

    public function show_one_maintenances_info($MaintenanceID)
    {

        $CheckForMaintenance = Maintenance::find($MaintenanceID);

        if ($CheckForMaintenance) {

            $Response['data'] = $CheckForMaintenance;
            $Response['success'] = true;
            $Response['message'] = 'Maintenance bill fetched successfully.';
            $Response['status'] = 200;

            return response()->json($Response, 200);
        } else {

            $Response['data'] = null;
            $Response['success'] = false;
            $Response['message'] = 'We could not identify your maintenance bill.';
            $Response['status'] = 404;

            return response()->json($Response, 404);
        }
    }
}
