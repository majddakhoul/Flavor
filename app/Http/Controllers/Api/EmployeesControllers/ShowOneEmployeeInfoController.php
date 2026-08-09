<?php

namespace App\Http\Controllers\Api\EmployeesControllers;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Support\Facades\Crypt;

class ShowOneEmployeeInfoController extends Controller
{

    public function show_one_employee_info($EmployeeId){

        $Employee = Employee::find($EmployeeId);
        if(!$Employee){
            $Response['data'] = null;
            $Response['success'] = false;
            $Response['message'] = 'There is no employee with this info.';
            $Response['status'] = 404;

            return response()->json($Response, 404);
        }
        $DecryptedNationalId = Crypt::decryptString($Employee->national_id);

        if($Employee){
            $EmployeeFetch =[

                'national_id' => $DecryptedNationalId,
                'salary' => $Employee->salary,
                'bonus' => $Employee->bonus,
                'hire_date' => $Employee->hire_date,
                'birth_date' => $Employee->birth_date,
                'position' => $Employee->position,
                'notes' => $Employee->notes,
                'user_id' => $Employee->user_id,
            ];

            $Response['data'] = $EmployeeFetch;
            $Response['success'] = true;
            $Response['message'] = 'Employee data fetched successfully.';
            $Response['status'] = 200;

            return response()->json($Response, 200);

        }
    }

}
