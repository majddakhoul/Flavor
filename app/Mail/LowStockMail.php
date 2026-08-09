<?php

namespace App\Mail;

class LowStockMail extends FlavorMailable
{
    protected function subjectKey(): string
    {
        return 'inventory.low-stock';
    }
}
