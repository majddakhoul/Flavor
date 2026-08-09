<?php

namespace App\Mail;

class VerificationCodeMail extends FlavorMailable
{
    protected function subjectKey(): string
    {
        return 'auth.verification';
    }
}
