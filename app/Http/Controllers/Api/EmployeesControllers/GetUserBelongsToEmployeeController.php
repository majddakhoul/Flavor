<?php

namespace App\Http\Controllers\Api\EmployeesControllers;

use App\Models\Employee;

class GetUserBelongsToEmployeeController
{

    public function get_user_belongs_to_employee($EmployeeId){

        $Employee = Employee::find($EmployeeId);

        if(!$Employee){

            $Response['data'] = null;
            $Response['success'] = false;
            $Response['message'] = 'No employee found with the specified attributes.';
            $Response['status'] = 404;
            return response()->json($Response, 404);
        }

        $User = $Employee->user()->get()->first();

        if(!$User){

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
