<?php

namespace App\Http\Controllers\Api\UsersControllers\CrudControllers;

use App\Models\User;
use App\Notifications\Auth\UserAccountDeletedNotification;
class DeleteAccountForUserController
{
    public function delete_account_for_user(){

        $UserId = auth()->user()->id;
        $User = User::find($UserId);
        if (!$User){

                $Response['data'] = null;
                $Response['success'] = false;
                $Response['message'] = 'User not authenticated.';
                $Response['status'] = 401;

                return response()->json($Response, 401);
        }

        if($User->tokens()->delete()){
                $User->notify(new UserAccountDeletedNotification($User));
            if($User->delete()){

                $Response['data'] = null;
                $Response['success'] = true;
                $Response['message'] = 'User account deleted successfully.';
                $Response['status'] = 200;

                return response()->json($Response,200);

            }else{

                $Response['data'] = null;
                $Response['success'] = false;
                $Response['message'] = 'An error occurred, You can try again later.';
                $Response['status'] = 400;

                return response()->json($Response,400);

            }
        }else{

            $Response['data'] = null;
                $Response['success'] = false;
                $Response['message'] = 'An error occurred, You can try again later.';
                $Response['status'] = 400;

                return response()->json($Response,400);

        }

    }

}
