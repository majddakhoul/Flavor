<?php

namespace App\Http\Controllers\Api\UsersControllers\AuthControllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Notifications\Auth\LoginNotification;
use App\Http\Requests\Api\UsersRequests\AuthRequests\LoginAllUsersRequest;
use Illuminate\Support\Facades\Session;

class LoginAllUsersController extends Controller
{
    public function login_all_users(LoginAllUsersRequest $LoginAllUsersRequest){

        $LoginAllUsersRequest->validated();

        $Credentials = $LoginAllUsersRequest->only('email','password');

        if(auth()->attempt($Credentials)){

            $User = User::where('email',$Credentials['email'])->first();

            if (!$User){

                $Response['data'] = null;
                $Response['success'] = false;
                $Response['message'] = 'Email or password is not correct.';
                $Response['status'] = 400;

                return response()->json($Response, 400);
            }

            if($User-> status == true){
                 $Response['data'] = null;
                $Response['success'] = false;
                $Response['message'] = 'User is already logged in.';
                $Response['status'] = 400;

                return response()->json($Response, 400);
            }
            $User->tokens()->delete();
            $User->status = true;
            if($User->save()){

                $cartKey = 'cart_' . $User->id;
                if (!Session::has($cartKey)) {
                    Session::put($cartKey, [
                        'meals' => [],
                        'offers' => [],
                    ]);
                }
                if($User->user_type == 'Employee'){

                    $Employee = $User->employee()->first();
                    $Response['token'] = $User->createToken(request()->userAgent())->plainTextToken;
                    $Response['first_name'] = $User->first_name;
                    $Response['last_name'] = $User->last_name;
                    $Response['user_type'] = $User->user_type;
                    $Response['postion'] = $Employee->position;
                    $Response['success'] = true;
                    $Response['status'] = 200;

            $User->notify(new LoginNotification($User));

            return response()->json($Response, 200);
            }
            $Response['token'] = $User->createToken(request()->userAgent())->plainTextToken;
            $Response['first_name'] = $User->first_name;
            $Response['last_name'] = $User->last_name;
            $Response['user_type'] = $User->user_type;
            $Response['success'] = true;
            $Response['status'] = 200;

            $User->notify(new LoginNotification($User));

            return response()->json($Response, 200);

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
                $Response['message'] = 'Email or password is not correct.';
                $Response['status'] = 400;

                return response()->json($Response, 400);

         }
    }

}

