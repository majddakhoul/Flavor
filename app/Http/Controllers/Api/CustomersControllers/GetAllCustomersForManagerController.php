<?php

namespace App\Http\Controllers\Api\CustomersControllers;

use App\Models\Customer;

class GetAllCustomersForManagerController
{

    public function get_all_customers_for_manager()
    {
        $Customers = Customer::all();
        if ($Customers->isNotEmpty()) {

            $AllCustomerData = array();
            foreach ($Customers as $Customer) {
                $User = $Customer->user()->get()->first();
                $CustomerFetch = [
                    'first_name' => $User->first_name,
                    'last_name' => $User->last_name,
                    'email' => $User->email,
                    'status' => $User->status,
                    'phone' => $User->phone,
                    'gender' => $User->gender,
                    'location_id' => $User->location_id,
                    'create_at' => $User->created_at,
                    'user_id' => $User->id,
                    'id' => $Customer->id,
                    'allergies' => $Customer->allergies,
                    'favorite_categories' => $Customer->favorite_categories,
                    'ban' => $Customer->ban,
                    'ban_date' => $Customer->ban_date,
                ];

                array_push($AllCustomerData, $CustomerFetch);
            }
            $Response['data'] = $AllCustomerData;
            $Response['success'] = true;
            $Response['message'] = 'Customers fetched successfully.';
            $Response['status'] = 200;

            return response()->json($Response, 200);
        } elseif ($Customers->isEmpty()) {

            $Response['data'] = null;
            $Response['success'] = true;
            $Response['message'] = 'There is nothing to display.';
            $Response['status'] = 200;

            return response()->json($Response, 200);
        } else {

            $Response['data'] = null;
            $Response['success'] = false;
            $Response['message'] = 'An error occurred, You can try again later.';
            $Response['status'] = 400;

            return response()->json($Response, 400);
        }
    }
}
