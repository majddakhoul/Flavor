<?php

namespace App\Http\Controllers\Api\UsersControllers\AuthControllers;


use App\Models\User;
use Ichtrojan\Otp\Otp;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Notifications\Auth\PasswordChanged;
use App\Http\Requests\Api\UsersRequests\AuthRequests\ResetPasswordRequest;

class ResetPasswordController extends Controller
{
    private $otp;

    public function __construct()
    {
        $this->otp = new Otp;
    }

    public function password_rest(ResetPasswordRequest $ResetPasswordRequest)
    {
        $ResetPasswordRequest->validated();
        $OtpValidationResult = $this->otp->validate($ResetPasswordRequest->email, $ResetPasswordRequest->otp);

        if (!$OtpValidationResult->status) {

            $Response['success'] = false;
            $Response['status'] = 401;
            $Response['message'] = ['errors' => $OtpValidationResult];
            $Response['data'] = null;

            return response()->json($Response, 401);

        }

        $User = User::where('email',$ResetPasswordRequest->email)->first();

        $User->update(['password'=>Hash::make($ResetPasswordRequest->password)]);
        $User->tokens()->delete();

        $Response['success'] = true;
        $Response['status'] = 200;
        $Response['message'] = 'Your password has been updated successfully.';
        $Response['data'] = null;

        $User->notify(new PasswordChanged($User->first_name));

        return response()->json($Response, 200);

    }
}
