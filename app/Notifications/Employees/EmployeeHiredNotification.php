<?php

namespace App\Notifications\Employees;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\User;

class EmployeeHiredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $user;
    protected $password;
    protected $startDate;
    protected $appName;

    public function __construct(User $user, $password, $startDate)
    {
        $this->user = $user;
        $this->password = $password;
        $this->startDate = $startDate;
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
            ->subject('🎉 You Have Been Hired!')
            ->view('emails.EmployeeHired', [
                'user' => $this->user,
                'password' => $this->password,
                'startDate' => $this->startDate,
                'appName' => $this->appName,
            ]);
    }
}
