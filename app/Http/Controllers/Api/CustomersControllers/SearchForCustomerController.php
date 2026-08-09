<?php

namespace App\Http\Controllers\Api\CustomersControllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SearchForCustomerController
{
    public function search_for_customer(Request $request)
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

        $users = User::where(function($query) use ($searchTerm) {
                $query->where("first_name", "like", "%" . $searchTerm . "%")
                    ->orWhere("last_name", "like", "%" . $searchTerm . "%")
                    ->orWhere("phone", "like", "%" . $searchTerm . "%");
            })
            ->whereNotIn('user_type', ['Employee', 'Manager'])
            ->with('customer')
            ->get();

        foreach ($users as $user) {
            $customer = $user->customer;

            if ($customer) {
                $allResults[] = [
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'status' => $user->status,
                    'phone' => $user->phone,
                    'gender' => $user->gender,
                    'location_id' => $user->location_id,
                    'created_at' => $user->created_at,
                    'user_id' => $user->id,
                    'id' => $customer->id,
                    'allergies' => $customer->allergies,
                    'favorite_categories' => $customer->favorite_categories,
                     'ban' => $customer->ban,
                    'ban_date' => $customer->ban_date,
                ];
            }
        }

        if (empty($allResults)) {
            return response()->json([
                'data' => null,
                'success' => true,
                'message' => 'There is nothing to display.',
                'status' => 200
            ], 200);
        }

        return response()->json([
            'data' => $allResults,
            'success' => true,
            'message' => 'All results fetched successfully.',
            'status' => 200
        ], 200);
    }
}
