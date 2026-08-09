<?php

namespace App\Http\Controllers\Api\EmployeesControllers;

use App\Models\Employee;

class DeleteEmployeeFromManagerController
{

    public function delete_employee_from_manager($Employee_id){

        $Employee = Employee::find($Employee_id);

        if(!$Employee){

            $Response['data'] = null;
            $Response['success'] = false;
            $Response['message'] = 'No employee found with the specified attributes.';
            $Response['status'] = 404;

            return response()->json($Response, 404);
        }

        $User = $Employee->user;

        if(!$User){
            $Response['data'] = null;
            $Response['success'] = false;
            $Response['message'] = 'No employee found with the specified attributes.';
            $Response['status'] = 404;

            return response()->json($Response, 404);
        }

        if($User->delete()){

            $Response['data'] = null;
            $Response['success'] = true;
            $Response['message'] = 'Employee deleted successfully.';
            $Response['status'] = 200;

            return response()->json($Response, 200);
        }
    }
}
