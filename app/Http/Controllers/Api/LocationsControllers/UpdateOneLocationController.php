<?php

namespace App\Http\Controllers\Api\LocationsControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LocationsRequests\UpdateLocationRequest;
use App\Models\Location;

class UpdateOneLocationController extends Controller
{
    public function update_one_location(UpdateLocationRequest $UpdateLocationRequest){

        $ValidatedData = $UpdateLocationRequest->validated();
        $LocationId = $UpdateLocationRequest->location_id;

        $Location = Location::find($LocationId);

        if($Location){

            $UpdateData = [
            'country' => $ValidatedData['country'] ?? $Location->country,
            'state' => $ValidatedData['state'] ?? $Location->state,
            'city' => $ValidatedData['city'] ?? $Location->city,
            'region' => $ValidatedData['region'] ?? $Location->region,
            'street' => $ValidatedData['street'] ?? $Location->street,
            'delivery_time' => $ValidatedData['delivery_time'] ?? $Location->delivery_time,
            ];

            $Updated = $Location->update($UpdateData);

            if ($Updated) {

                $Response['data'] = $Location->fresh();
                $Response['success'] = true;
                $Response['message'] = 'Location updated successfully.';
                $Response['status'] = 200;

                return response()->json($Response, 200);

            }
            else{

                $Response['data'] = null;
                $Response['success'] = false;
                $Response['message'] = 'Failed to update location.';
                $Response['status'] = 400;

                return response()->json($Response, 400);

            }
    }
    else{

            $Response['data'] = null;
            $Response['success'] = false;
            $Response['message'] = 'We could not identify the site.';
            $Response['status'] = 404;

            return response()->json($Response,404);

        }

    }

}
