<?php

namespace App\Http\Controllers\Api\LocationsControllers;

use App\Models\Location;
use App\Http\Controllers\Controller;

class DeleteOneLocationController extends Controller
{
    public function delete_one_location($LocationId){

        $CheckForLocation = Location::find($LocationId);

        if($CheckForLocation){

            if($CheckForLocation->delete()){

                $Response['data'] = null;
                $Response['success'] = true;
                $Response['message'] = 'Location has been successfully deleted.';
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
            $Response['message'] = 'We could not identify your location.';
            $Response['status'] = 404;

            return response()->json($Response,404);

        }

    }

}
