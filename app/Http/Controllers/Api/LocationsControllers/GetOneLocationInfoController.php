<?php

namespace App\Http\Controllers\Api\LocationsControllers;

use App\Models\Location;
use App\Http\Controllers\Controller;

class GetOneLocationInfoController extends Controller
{
     public function get_one_location_info($LocationId){

        $CheckForLocation = Location::find($LocationId);

        if($CheckForLocation){

            $Response['data'] = $CheckForLocation;
            $Response['success'] = true;
            $Response['message'] = 'Location fetched successfully.';
            $Response['status'] = 200;

            return response()->json($Response,200);

        }
        else{

            $Response['data'] = null;
            $Response['success'] = false;
            $Response['message'] = 'We could not identify your location.';
            $Response['status'] = 404;

            return response()->json($Response,404);

        }

    }

}
