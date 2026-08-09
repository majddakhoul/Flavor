<?php

namespace App\Http\Controllers\Api\LocationsControllers;

use App\Models\Location;
use App\Http\Controllers\Controller;

class GetAllLocationsController extends Controller
{
    public function get_all_locations(){

        $AllLocations = Location::all();

        if($AllLocations->isNotEmpty()){

            $Response['data'] = $AllLocations;
            $Response['success'] = true;
            $Response['message'] = 'All locations fetched successfully.';
            $Response['status'] = 200;

            return response()->json($Response,200);

        }
        elseif($AllLocations->isEmpty()){

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
