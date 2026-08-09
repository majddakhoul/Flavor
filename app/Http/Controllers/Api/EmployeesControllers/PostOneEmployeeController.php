<?php

namespace App\Http\Controllers\Api\EmployeesControllers;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\EmployeesRequests\PostOneEmployeeRequest;
use App\Notifications\Employees\EmployeeHiredNotification;

class PostOneEmployeeController extends Controller
{

    public function post_one_employee(PostOneEmployeeRequest $PostOneEmployeeRequest)
    {

        $PostOneEmployeeRequest->validated();

        $Password = Str::random(16);

        $NewUser['password'] = Hash::make($Password);
        $NewUser['last_name'] = $PostOneEmployeeRequest->last_name;
        $NewUser['first_name'] = $PostOneEmployeeRequest->first_name;
        $NewUser['phone'] = $PostOneEmployeeRequest->phone;
        $NewUser['email'] = $PostOneEmployeeRequest->email;
        $NewUser['gender'] = $PostOneEmployeeRequest->gender;
        $NewUser['location_id'] = $PostOneEmployeeRequest->location_id;
        $NewUser['email_verified_at'] = now();
        $NewUser['user_type'] = 'Employee';
        $User = User::create($NewUser);

        if ($User->save()) {
            $NewEmployee['national_id'] = Crypt::encrypt($PostOneEmployeeRequest->national_id);
            $NewEmployee['salary'] = $PostOneEmployeeRequest->salary;
            $NewEmployee['bonus'] = $PostOneEmployeeRequest->bonus;
            $NewEmployee['hire_date'] = $PostOneEmployeeRequest->hire_date;
            $NewEmployee['position'] = $PostOneEmployeeRequest->position;
            $NewEmployee['notes'] = $PostOneEmployeeRequest->notes;
            $NewEmployee['birth_date'] = $PostOneEmployeeRequest->birth_date;
            $NewEmployee['user_id'] = $User->id;

            $Employee = Employee::create($NewEmployee);

            if ($Employee->save()) {

                $User->notify(new EmployeeHiredNotification($User, $Password, $PostOneEmployeeRequest->hire_date));

                $decryptedNationalId = Crypt::decrypt($Employee->national_id);
                $profileData = [
                    'employee_id' => $Employee->id,
                    'user_id' => $Employee->user_id,
                    'first_name' => $Employee->user->first_name,
                    'last_name' => $Employee->user->last_name,
                    'email' => $Employee->user->email,
                    'phone' => $Employee->user->phone,
                    'gender' => $Employee->user->gender,
                    'national_id' => $decryptedNationalId,
                    'salary' => $Employee->salary,
                    'bonus' => $Employee->bonus,
                    'position' => $Employee->position,
                    'hire_date' => $Employee->hire_date,
                    'birth_date' => $Employee->birth_date,
                    'notes' => $Employee->notes,
                    'location_id' => $Employee->user->location_id,
                    'user_type' => $Employee->user->user_type,
                    'created_at' => $Employee->created_at,
                    'updated_at' => $Employee->updated_at
                ];

                $Response['data'] = $profileData;
                $Response['success'] = true;
                $Response['message'] = 'Employee created and notified successfully.';
                $Response['status'] = 201;

                return response()->json($Response, 201);
            } else {

                $Response['data'] = null;
                $Response['success'] = false;
                $Response['message'] = 'An error occurred, You can try again later.';
                $Response['status'] = 400;

                return response()->json($Response, 400);
            }
        } else {

            $Response['data'] = null;
            $Response['success'] = false;
            $Response['message'] = 'An error occurred, You can try again later.';
            $Response['status'] = 400;

            return response()->json($Response, 400);
        }
    }
}
