<?php

namespace App\Http\Controllers\Api\EmployeesControllers;

use Illuminate\Support\Facades\Crypt;
use App\Models\Employee;
use App\Http\Requests\Api\EmployeesRequests\UpdateEmployeeInfoFromManagerRequest;
use App\Notifications\Auth\UserAccountUpdatedNotification;

class UpdateEmployeeInfoFromManagerController
{
    public function update_employee_info_from_manager(UpdateEmployeeInfoFromManagerRequest $UpdateEmployeeInfoFromManagerRequest)
    {
        $ValidatedData = $UpdateEmployeeInfoFromManagerRequest->validated();

        $Employee = Employee::find($UpdateEmployeeInfoFromManagerRequest->employee_id);

        if(!$Employee) {
            $Response['data'] = null;
            $Response['success'] = false;
            $Response['message'] = 'No employee found with the specified attributes.';
            $Response['status'] = 404;

            return response()->json($Response, 404);
        }

        $UpdateData = [
            'salary' => $ValidatedData['salary'] ?? $Employee->salary,
            'bonus' => $ValidatedData['bonus'] ?? $Employee->bonus,
            'notes' => $ValidatedData['notes'] ?? $Employee->notes,
            'position' => $ValidatedData['position'] ?? $Employee->position,
        ];

        $Updated = $Employee->update($UpdateData);
        $User = $Employee->user()->first();

        if ($Updated) {
            $Response['data'] = $Employee->fresh()->makeHidden('national_id');
            $Response['success'] = true;
            $Response['message'] = 'Employee information updated successfully.';
            $Response['status'] = 200;
            $User->notify(new UserAccountUpdatedNotification($User));
            return response()->json($Response, 200);
        } else {
            $Response['data'] = null;
            $Response['success'] = false;
            $Response['message'] = 'Failed to update your employee information.';
            $Response['status'] = 400;

            return response()->json($Response, 400);
        }
    }
}
