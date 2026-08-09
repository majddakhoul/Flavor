<?php

namespace App\Mail;

class EmployeeCredentialsMail extends FlavorMailable
{
    protected function subjectKey(): string
    {
        return 'people.credentials';
    }
}
