<?php

namespace App\Http\Controllers\Api\MaintenancesControllers;

use App\Models\Maintenance;

class DeleteMaintenanceController
{

    public function delete_maintenance($MaintenanceId){
        $CheckForMaintenance = Maintenance::find($MaintenanceId);

        if($CheckForMaintenance){

            if($CheckForMaintenance->delete()){

                $Response['data'] = null;
                $Response['success'] = true;
                $Response['message'] = 'Maintenance information has been successfully deleted.';
                $Response['status'] = 200;

                return response()->json($Response,200);

            }
            else{

                $Response['data'] = null;
                $Response['success'] = false;
                $Response['message'] = 'An error occurred, You can try again later.';
                $Response['status'] = 400;

                return response()->json($Response,400);

            }

        }
        else{

            $Response['data'] = null;
            $Response['success'] = false;
            $Response['message'] = 'We could not identify your maintenance info.';
            $Response['status'] = 404;

            return response()->json($Response,404);

        }
    }
}
