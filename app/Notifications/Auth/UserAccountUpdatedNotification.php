<?php

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class UserAccountUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $user;

    public function __construct(User $user)
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
        $updateDate = now()->format('F j, Y \a\t g:i A');

        return (new MailMessage)
            ->mailer('smtp')
            ->subject("🔄 Your Account Was Updated – $appName")
            ->view('emails.UserAccountUpdatedNotification', [
                'user' => $this->user,
                'updateDate' => $updateDate,
                'appName' => $appName,
            ])
            ->line('If you did not make this change, please contact support immediately.')
            ->salutation('Warm regards, The ' . $appName . ' Team');
    }
}
