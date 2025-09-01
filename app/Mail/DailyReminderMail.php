<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailyReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $email
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⏰ Daily Reminder: Log Your Reps!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.daily-reminder',
            with: [
                'email' => $this->email,
                'loginUrl' => route('login'),
                'today' => now()->format('l, F j, Y'),
            ],
        );
    }
}
