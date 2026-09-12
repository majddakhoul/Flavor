<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;

abstract class FlavorNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected readonly array $payload = [])
    {
        $this->onQueue(config('flavor.queues.mail'));
    }

    abstract protected function subjectKey(): string;

    protected function channels(): array
    {
        return ['mail', 'database'];
    }

    protected function mailable(string $locale): ?Mailable
    {
        return null;
    }

    protected function icon(): string
    {
        return 'bell';
    }

    protected function tone(): string
    {
        return 'info';
    }

    protected function url(): ?string
    {
        return $this->payload['action']['url'] ?? null;
    }

    protected function recipientLocale(object $notifiable): string
    {
        return method_exists($notifiable, 'notificationLocale')
            ? $notifiable->notificationLocale()
            : (string) config('app.fallback_locale');
    }

    public function via(object $notifiable): array
    {
        return $this->channels();
    }

    public function toMail(object $notifiable): ?Mailable
    {
        $mailable = $this->mailable($this->recipientLocale($notifiable));

        return $mailable?->to($notifiable->email);
    }

    public function toDatabase(object $notifiable): array
    {
        $locale = $this->recipientLocale($notifiable);
        $params = $this->payload['subject_data'] ?? [];

        return [
            'key' => $this->subjectKey(),
            'title' => __('notifications.' . $this->subjectKey() . '.title', $params, $locale),
            'body' => __('notifications.' . $this->subjectKey() . '.body', $params, $locale),
            'icon' => $this->icon(),
            'tone' => $this->tone(),
            'url' => $this->url(),
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
