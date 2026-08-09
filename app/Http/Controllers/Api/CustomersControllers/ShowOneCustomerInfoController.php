<?php

namespace App\Http\Controllers\Api\CustomersControllers;

use App\Models\Customer;

class ShowOneCustomerInfoController
{
    public function show_one_customer_info($CustomerId){

        $Customer = Customer::find($CustomerId);

        if($Customer){

            $CustomerFetch =[

                'allergies' => $Customer->allergies,
                'favorite_categories' => $Customer->favorite_categories,
                'user_id' => $Customer->user_id,
                'ban' => $Customer->ban,
                'ban_date' => $Customer->ban_date,
        ];
            $Response['data'] = $CustomerFetch;
            $Response['success'] = true;
            $Response['message'] = 'Customer data fetched successfully.';
            $Response['status'] = 200;

            return response()->json($Response, 200);

        }else{

            $Response['data'] = null;
            $Response['success'] = false;
            $Response['message'] = 'No Customer found with the specified attributes.';
            $Response['status'] = 404;

            return response()->json($Response, 404);
        }

    }

    }


