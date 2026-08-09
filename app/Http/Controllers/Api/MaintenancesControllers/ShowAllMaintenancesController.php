<?php

namespace App\Http\Controllers\Api\MaintenancesControllers;

use App\Models\Maintenance;

class ShowAllMaintenancesController
{

    public function show_all_maintenances(){

        $AllMaintenances = Maintenance::all();

        if($AllMaintenances->isNotEmpty()){

            $Response['data'] = $AllMaintenances;
            $Response['success'] = true;
            $Response['message'] = 'All maintenance bills fetched successfully.';
            $Response['status'] = 200;

            return response()->json($Response,200);

        }
        elseif($AllMaintenances->isEmpty()){

            $Response['data'] = null;
            $Response['success'] = true;
            $Response['message'] = 'There is nothing to display.';
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

}
