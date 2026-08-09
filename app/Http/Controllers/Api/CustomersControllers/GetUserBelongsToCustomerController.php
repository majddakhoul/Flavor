<?php

namespace App\Http\Controllers\Api\CustomersControllers;

use App\Models\Customer;

class GetUserBelongsToCustomerController
{

    public function get_user_belongs_to_customer($Customer_id)
    {
        $Customer = Customer::find($Customer_id);

        if (!$Customer) {

            $Response['data'] = null;
            $Response['success'] = false;
            $Response['message'] = 'No Customer found with the specified attributes.';
            $Response['status'] = 404;
            return response()->json($Response, 404);
        }

        $User = $Customer->user()->get()->first();

        if (!$User) {

            $Response['data'] = null;
            $Response['success'] = false;
            $Response['message'] = 'No user found with the specified attributes.';
            $Response['status'] = 404;
            return response()->json($Response, 404);
        }

        $Response['data'] = $User;
        $Response['success'] = true;
        $Response['message'] = 'User data fetched successfully.';
        $Response['status'] = 200;
        return response()->json($Response,  200);
    }
}
