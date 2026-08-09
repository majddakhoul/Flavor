<?php

namespace App\Listeners;

use App\Events\VerificationCodeIssued;
use App\Mail\VerificationCodeMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendVerificationCode implements ShouldQueue
{
    public function handle(VerificationCodeIssued $event): void
    {
        Mail::to($event->user->email)->send(new VerificationCodeMail([
            'subject_data' => ['name' => $event->user->first_name],
            'highlight' => $event->code,
            'rows' => [
                ['label' => __('domain.valid_for'), 'value' => __('domain.minutes', ['count' => $event->minutes])],
            ],
            'action' => ['label' => __('domain.verify_email'), 'url' => route('verification.notice')],
        ], app()->getLocale()));
    }
}
