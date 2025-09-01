<?php

namespace App\Mail;

use App\Models\MagicLink;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MagicLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public MagicLink $magicLink
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your 100ADayChallenge Magic Link',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.magic-link',
            with: [
                'magicLink' => $this->magicLink,
                'loginUrl' => route('magic-link.verify', $this->magicLink->token),
            ],
        );
    }
}
