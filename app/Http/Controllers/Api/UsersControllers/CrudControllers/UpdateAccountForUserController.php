<?php

namespace App\Http\Controllers\Api\UsersControllers\CrudControllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\Auth\UserAccountUpdatedNotification;
use App\Http\Requests\Api\UsersRequests\CrudRequests\UpdateAccountForUserRequest;

class UpdateAccountForUserController extends Controller
{
    public function update_account_for_user(UpdateAccountForUserRequest $updateAccountForUserRequest){

        $ValidatedData = $updateAccountForUserRequest->validated();

        $UserId = auth()->user()->id;

        $User = User::find($UserId);

        $UpdateData = [
            'first_name' => $ValidatedData['first_name'] ?? $User->first_name,
            'last_name' => $ValidatedData['last_name'] ?? $User->last_name,
            'phone' => $ValidatedData['phone'] ?? $User->phone,
            'gender' => $ValidatedData['gender'] ?? $User->gender,
            'birth_date' => $ValidatedData['birth_date'] ?? $User->birth_date,
            'location_id' => $ValidatedData['location_id'] ?? $User->location_id,
            ];

        $Updated = $User->update($UpdateData);

            if ($Updated) {

                $Response['data'] = $User->fresh();
                $Response['success'] = true;
                $Response['message'] = 'User information updated successfully.';
                $Response['status'] = 200;
                $User->notify(new UserAccountUpdatedNotification($User));
                return response()->json($Response, 200);

            }else{

                $Response['data'] = null;
                $Response['success'] = false;
                $Response['message'] = 'Failed to update your information.';
                $Response['status'] = 400;

                return response()->json($Response, 400);

            }
    }
}
