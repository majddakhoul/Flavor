<?php

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordChanged extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $firstName;
    protected string $appName;
    protected ?string $appUrl;

    public function __construct(string $firstName, string $appName = 'Flavor', ?string $appUrl = null)
    {
        $this->firstName = $firstName;
        $this->appName = $appName;
        $this->appUrl = $appUrl ?? config('app.url');
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Password Has Been Changed')
            ->view('emails.PasswordChanged', [
                'user' => (object)['first_name' => $this->firstName],
                'appName' => $this->appName,
                'appUrl' => $this->appUrl,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
