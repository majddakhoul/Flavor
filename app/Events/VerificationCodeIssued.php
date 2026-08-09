<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VerificationCodeIssued
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly \App\Models\User $user,
        public readonly string $code,
        public readonly int $minutes,
    ) {
    }
}
