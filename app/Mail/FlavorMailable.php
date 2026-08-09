<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

abstract class FlavorMailable extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly array $payload,
        public readonly string $locale = 'en',
    ) {
        $this->onQueue(config('flavor.queues.mail'));
        $this->locale($locale);
    }

    abstract protected function subjectKey(): string;

    public function envelope(): Envelope
    {
        return new Envelope(subject: __('mail.' . $this->subjectKey() . '.subject', $this->payload['subject_data'] ?? []));
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.message',
            with: [
                'heading' => __('mail.' . $this->subjectKey() . '.heading', $this->payload['subject_data'] ?? []),
                'intro' => __('mail.' . $this->subjectKey() . '.intro', $this->payload['subject_data'] ?? []),
                'rows' => $this->payload['rows'] ?? [],
                'highlight' => $this->payload['highlight'] ?? null,
                'action' => $this->payload['action'] ?? null,
                'footnote' => $this->payload['footnote'] ?? null,
            ],
        );
    }
}
