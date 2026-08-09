<?php

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class LoginNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $appName = config('app.name');

        return (new MailMessage)
            ->mailer('smtp')
            ->subject("Successful Login - {$appName}")
            ->view('emails.LoginNotification', [
                'user' => $this->user,
                'loginTime' => now()->format('Y-m-d H:i:s'),
            ]);
    }
}
