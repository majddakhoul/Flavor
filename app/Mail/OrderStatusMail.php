<?php

namespace App\Mail;

class OrderStatusMail extends FlavorMailable
{
    protected function subjectKey(): string
    {
        return 'orders.status';
    }
}
