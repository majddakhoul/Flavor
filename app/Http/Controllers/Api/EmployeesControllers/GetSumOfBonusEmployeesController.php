<?php

namespace App\Http\Controllers\Api\EmployeesControllers;

use App\Models\Employee;

class GetSumOfBonusEmployeesController
{
    public function get_sum_of_bonus_employees(){

    $Employees = Employee::all();

            if($Employees->isEmpty()){
                    $Response['data'] = ['sum' => 0];
                    $Response['success'] = true;
                    $Response['message'] = 'There is any data to get sum.';
                    $Response['status'] = 200;

                    return response()->json($Response,200);
            }
            $Sum = 0;
            foreach($Employees as $Employee){

                $Sum = $Employee->bonus + $Sum;
            }

                    $Response['data'] = $Sum;
                    $Response['success'] = true;
                    $Response['message'] = 'Sum of the bonuses fetched successfully.';
                    $Response['status'] = 200;

                    return response()->json($Response,200);
        }

}


