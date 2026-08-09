<?php

namespace App\Http\Controllers\Api\EmployeesControllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class SearchForEmployeesController
{
    public function search_for_employees(Request $request)
{
    $validator = Validator::make($request->all(), [
        'search' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'errors' => $validator->errors(),
        ], 422);
    }

    $searchTerm = $request->input('search');
    $allResults = [];

    // Search employees with their user data
    $employees = Employee::with('user')
        ->where(function($query) use ($searchTerm) {
            $query->where("hire_date", "like", "%".$searchTerm."%")
                ->orWhere("position", "like", $searchTerm )
                ->orWhere("salary", "=", $searchTerm)
                ->orWhere("bonus", "=", $searchTerm)
                ->orWhere("birth_date", "like", "%".$searchTerm."%");
        })
        ->get();

    // Get user IDs from employees to exclude from user search
    $employeeUserIds = $employees->pluck('user_id')->toArray();

    // Search users not already found in employees
    $users = User::where(function($query) use ($searchTerm) {
            $query->where("first_name", "like", "%".$searchTerm."%")
                ->orWhere("last_name", "like", "%".$searchTerm."%")
                ->orWhere("phone", "like", "%".$searchTerm."%");
        })
        ->whereNotIn('user_type', ['Customer', 'Manager'])
        ->whereNotIn('id', $employeeUserIds)
        ->whereHas('employee') // Only users who are employees
        ->get();


    foreach ($employees as $employee) {
        if (!$employee->user) continue;

        try {
            $decryptedNationalId = Crypt::decryptString($employee->national_id);
        } catch (\Exception $e) {
            $decryptedNationalId = 'Invalid encryption';
        }

        $allResults[] = [
            'id' => $employee->id,
            'national_id' => $decryptedNationalId,
            'salary' => $employee->salary,
            'bonus' => $employee->bonus,
            'hire_date' => $employee->hire_date,
            'position' => $employee->position,
            'notes' => $employee->notes,
            'first_name' => $employee->user->first_name,
            'last_name' => $employee->user->last_name,
            'email' => $employee->user->email,
            'status' => $employee->user->status,
            'phone' => $employee->user->phone,
            'gender' => $employee->user->gender,
            'birth_date' => $employee->user->birth_date,
            'location_id' => $employee->user->location_id,
            'created_at' => $employee->user->created_at,
            'user_id' => $employee->user->id,
        ];
    }

    // Process users not already included as employees
    foreach ($users as $user) {
        $employee = $user->employee;
        if (!$employee) continue;

        try {
            $decryptedNationalId = Crypt::decrypt($employee->national_id);
        } catch (\Exception $e) {
            $decryptedNationalId = 'Invalid encryption';
        }

        $allResults[] = [
            'id' => $employee->id,
            'national_id' => $decryptedNationalId,
            'salary' => $employee->salary,
            'bonus' => $employee->bonus,
            'hire_date' => $employee->hire_date,
            'position' => $employee->position,
            'notes' => $employee->notes,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'status' => $user->status,
            'phone' => $user->phone,
            'gender' => $user->gender,
            'birth_date' => $employee->user->birth_date,
            'location_id' => $user->location_id,
            'created_at' => $user->created_at,
            'user_id' => $user->id,
        ];
    }

    return response()->json([
        'success' => true,
        'status' => 200,
        'data' => $allResults,
        'message' => count($allResults) ? 'Search results fetched successfully' : 'No matching records found'
    ]);
}

}
