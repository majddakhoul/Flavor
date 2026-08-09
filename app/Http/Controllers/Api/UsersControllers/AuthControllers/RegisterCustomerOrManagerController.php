<?php

namespace App\Http\Controllers\Api\UsersControllers\AuthControllers;

use App\Models\Customer;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use App\Notifications\Auth\RegisterCustomerOrManagerNotification;
use App\Notifications\Auth\EmailVerificationForManagerOrCustomerNotification;
use App\Http\Requests\Api\UsersRequests\AuthRequests\RegisterCustomerOrManagerRequest;

class RegisterCustomerOrManagerController extends Controller
{
    public function register_customer_or_manager(RegisterCustomerOrManagerRequest $RegisterCustomerOrManagerRequest)
    {
        $NewUser = $RegisterCustomerOrManagerRequest->validated();

        $NewUser['password'] = Hash::make($NewUser['password']);

        $User = User::create($NewUser);
        $GetUser = User::find($User->id);
        if($GetUser->save()){

            $Customer = new Customer();
            $Customer->user_id = $GetUser->id;

            if(!$Customer->save()){
                $Response['data'] = null;
                $Response['success'] = false;
                $Response['message'] = 'A problem occurred during the account creation process. Please try again later.';
                $Response['status'] = 400;

                return response()->json($Response,400);

            }
            $cartKey = 'cart_' . $GetUser->id;
            if (!Session::has($cartKey)) {
                Session::put($cartKey, [
                    'meals' => [],
                    'offers' => [],
                ]);
            }
            $Response['data'] = [
                'token' => $GetUser->createToken('user',['app:all'])->plainTextToken,
                'first_name' => $GetUser->first_name,
                'last_name' => $GetUser->last_name,
                'user_type' => $GetUser->user_type
            ];
            $Response['success'] = true;
            $Response['message'] = 'Your account registration has been completed. verify your account if you want to access the homepage.';
            $Response['status'] = 201;

            $User->notify(new RegisterCustomerOrManagerNotification($User));
            $User->notify(new EmailVerificationForManagerOrCustomerNotification());

            return response()->json($Response,201);

        }else{

            $Response['data'] = null;
            $Response['success'] = false;
            $Response['message'] ='A problem occurred during the account creation process. Please try again later.';
            $Response['status'] = 400;

            return response()->json($Response,400);

        }

    }

}
