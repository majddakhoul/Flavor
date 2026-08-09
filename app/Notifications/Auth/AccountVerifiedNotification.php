<?php

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\User;

class AccountVerifiedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $user;
    protected $appName;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->appName = config('app.name');
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->mailer('smtp')
            ->subject('✅ Your Account Has Been Verified')
            ->view('emails.AccountVerificationSuccess', [
                'user' => $this->user,
                'appName' => $this->appName,
            ]);
    }
}
