<?php

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class UserAccountDeletedNotification extends Notification implements ShouldQueue
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
        $deletedAt = now()->format('F j, Y \a\t g:i A');

        return (new MailMessage)
            ->mailer('smtp')
            ->subject("👋 We're sad to see you go – $appName")
            ->view('emails.UserAccountDeletedNotification', [
                'user' => $this->user,
                'deletedAt' => $deletedAt,
                'appName' => $appName,
            ])
            ->salutation("Take care,\nThe $appName Team");
    }
}
