<?php

namespace App\Http\Controllers\Api\UsersControllers\AuthControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UsersRequests\AuthRequests\ForgetPasswordRequest;
use App\Models\User;
use App\Notifications\Auth\ResetPasswordVerificationNotification;

class ForgetPasswordController extends Controller
{
    public function forget_password(ForgetPasswordRequest $ForgetPasswordRequest)
    {
        $ForgetPasswordRequest->validated();

        $Email = $ForgetPasswordRequest->only('email');

        $User = User::where('email',$Email)->first();

        $User->notify(new ResetPasswordVerificationNotification());

        $Response['success'] = true;
        $Response['status'] = 200;
        $Response['message'] = 'Please check your email. A password reset code has been sent.';
        $Response['data'] = null;

        return response()->json($Response, 200);

    }
}
