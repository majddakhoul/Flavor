<?php

namespace App\Http\Controllers\Api\UsersControllers\CrudControllers;

use App\Http\Controllers\Controller;

class ShowProfileForOneUserController extends Controller
{

    public function show_profile_for_one_user()
    {

        $User = auth()->user();

        if (!$User) {

            $Response['data'] = null;
            $Response['success'] = false;
            $Response['message'] = 'User not authenticated.';
            $Response['status'] = 401;

            return response()->json($Response, 401);
        }

        $Customer = $User->customer;
        $Employee = $User->employee;

        if (!$Customer && !$Employee) {

            $Response['data'] = [
                'first_name' => $User->first_name,
                'last_name' => $User->last_name,
                'email' => $User->email,
                'phone' => $User->phone,
                'gender' => $User->gender,
                'user_type' => $User->user_type,
                'birth_date' => $User->birth_date,
                'location_id' => $User->location_id,
                'create_at' => $User->created_at
            ];

            $Response['success'] = true;
            $Response['message'] = 'Account data fetched successfully.';
            $Response['status'] = 200;

            return response()->json($Response, 200);

        }

        if ($Customer) {

            $Response['data'] = [
                'first_name' => $User->first_name,
                'last_name' => $User->last_name,
                'email' => $User->email,
                'phone' => $User->phone,
                'gender' => $User->gender,
                'user_type' => $User->user_type,
                'birth_date' => $User->birth_date,
                'location_id' => $User->location_id,
                'customer_id' => $Customer->id,
                'create_at' => $User->created_at
            ];

            $Response['success'] = true;
            $Response['message'] = 'Account data fetched successfully.';
            $Response['status'] = 200;

            return response()->json($Response, 200);

        } else {

            $Response['data'] = [
                'first_name' => $User->first_name,
                'last_name' => $User->last_name,
                'email' => $User->email,
                'phone' => $User->phone,
                'gender' => $User->gender,
                'user_type' => $User->user_type,
                'birth_date' => $User->birth_date,
                'location_id' => $User->location_id,
                'employee_id' => $Employee->id,
                'create_at' => $User->created_at
            ];

            $Response['success'] = true;
            $Response['message'] = 'Account data fetched successfully.';
            $Response['status'] = 200;

            return response()->json($Response, 200);

        }

    }

}
