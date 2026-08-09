<?php

namespace App\Mail;

class OrderConfirmationMail extends FlavorMailable
{
    protected function subjectKey(): string
    {
        return 'orders.confirmation';
    }
}
