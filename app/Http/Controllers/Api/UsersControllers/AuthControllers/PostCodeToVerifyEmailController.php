<?php


namespace App\Http\Controllers\Api\UsersControllers\AuthControllers;

use App\Models\User;
use Ichtrojan\Otp\Otp;
use App\Http\Controllers\Controller;
use App\Notifications\Auth\AccountVerifiedNotification;
use App\Http\Requests\Api\UsersRequests\AuthRequests\PostCodeToVerifyEmailRequest;

class PostCodeToVerifyEmailController extends Controller
{

    private $otp;
    public function __construct()
    {
        $this->otp = new Otp;
    }

    public function post_code_to_verify_email(PostCodeToVerifyEmailRequest $PostCodeToVerifyEmailRequest){

        $PostCodeToVerifyEmailRequest->validated();
        $OtpValidationResult = $this->otp->validate($PostCodeToVerifyEmailRequest->email, $PostCodeToVerifyEmailRequest->otp);

        if (!$OtpValidationResult->status) {

            $Response['success'] = false;
            $Response['status'] = 401;
            $Response['message'] = ['errors' => $OtpValidationResult];
            $Response['data'] = null;

            return response()->json($Response, 401);

        }

        $User = User::where('email',$PostCodeToVerifyEmailRequest->email)->first();
        $Check = auth()->user()->email;
        if(!($Check === $PostCodeToVerifyEmailRequest->email))
        {
            $Response['data'] = null;
            $Response['success'] = false;
            $Response['message'] = 'An error occurred, You can try again later.';
            $Response['status'] = 400;

            return response()->json($Response,400);
        }
        $User->email_verified_at = now();
        $User->update();

        if (!$User) {

            $Response['data'] = null;
            $Response['success'] = false;
            $Response['message'] = 'No user found with this email address.';
            $Response['status'] = 404;

            return response()->json($Response, status: 404);
        }

        $Response['data'] = null;
        $Response['message'] = 'Email verified successfully.';
        $Response['status'] = 200;
        $Response['success'] = true;

        $User->notify(new AccountVerifiedNotification($User));

        return response()->json($Response, 200);

    }

}
