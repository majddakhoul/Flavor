<?php

namespace App\Http\Controllers\Api\EmployeesControllers;

use App\Models\Employee;
use Illuminate\Support\Facades\Crypt;
class ShowAllEmployeesInfoController
{

    public function show_all_employees_info(){

        $Employees = Employee::all();
        if($Employees->isNotEmpty()){

            $AllEmployeeData = array();
            foreach($Employees as $Employee){
                $DecryptedNationalId = Crypt::decryptString($Employee->national_id);
                $User = $Employee->user()->get()->first();
                $EmployeeFetch =[
                        'id' => $Employee->id,
                        'national_id' => $DecryptedNationalId,
                        'salary' => $Employee->salary,
                        'bonus' => $Employee->bonus,
                        'hire_date' => $Employee->hire_date,
                        'position' => $Employee->position,
                        'notes' => $Employee->notes,
                        'first_name' => $User->first_name,
                        'last_name' => $User->last_name,
                        'email' => $User->email,
                        'status' => $User->status,
                        'phone' => $User->phone,
                        'gender' => $User->gender,
                        'birth_date' => $Employee->birth_date,
                        'location_id' => $User->location_id,
                        'create_at' => $User->created_at,
                        'user_id' => $User->id,
                ];

                array_push($AllEmployeeData,$EmployeeFetch);
            }
            $Response['data'] = $AllEmployeeData;
            $Response['success'] = true;
            $Response['message'] = 'Employees info fetched successfully';
            $Response['status'] = 200;

            return response()->json($Response,200);

        }elseif($Employees->isEmpty()){

            $Response['data'] = null;
            $Response['success'] = true;
            $Response['message'] = 'There is nothing to display.';
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
}
