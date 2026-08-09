<?php

namespace App\Http\Controllers\Api\UsersControllers\AuthControllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\Auth\EmailVerificationForManagerOrCustomerNotification;

class ResendCodeToVerifyEmailController extends Controller
{
    public function resend_email_verification(Request $Request) {

        $Request->user()->notify(new EmailVerificationForManagerOrCustomerNotification());

        $Response['data'] = null;
        $Response['success'] = true;
        $Response['status'] = 200;
        $Response['message'] = 'We\'ve sent the verification code to your email. Please check your inbox.';

        return response()->json($Response, 200);

        }
}
