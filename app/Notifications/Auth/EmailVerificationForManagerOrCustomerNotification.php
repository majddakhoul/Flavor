<?php
namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Ichtrojan\Otp\Otp;

class EmailVerificationForManagerOrCustomerNotification extends Notification
{
    use Queueable;

    public $customMessage;
    public $subject;
    public $fromEmail;
    public $mailer;
    private $otp;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        $this->customMessage = 'Use the below code for verification process';
        $this->subject = 'Verification Needed';
        $this->fromEmail = "company.bussinuss@gmail.com";
        $this->mailer = 'smtp';
        $this->otp = new Otp();
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $otp = $this->otp->generate($notifiable->email, 'numeric', 6, 60);

        return (new MailMessage)
            ->mailer($this->mailer)
            ->subject($this->subject)
            ->view('emails.EmailVerificationForManagerOrCustomerNotification', [
                'user' => $notifiable,
                'customMessage' => $this->customMessage,
                'otp' => $otp->token,
                'appName' => config('app.name')
            ]);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}
