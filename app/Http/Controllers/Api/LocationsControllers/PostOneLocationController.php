<?php

namespace App\Http\Controllers\Api\LocationsControllers;

use App\Models\Location;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LocationsRequests\PostLocationRequest;

class PostOneLocationController extends Controller
{
    public function post_one_location(PostLocationRequest $PostLocationRequest){

        $ValidatedData = $PostLocationRequest->validated();

        $Location = Location::create($ValidatedData);

        if($Location){

            $Response['data'] = $Location;
            $Response['success'] = true;
            $Response['message'] = 'Location has been saved successfully.';
            $Response['status'] = 201;

            return response()->json($Response,201);

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
