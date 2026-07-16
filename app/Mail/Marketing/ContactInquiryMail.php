<?php

namespace App\Mail\Marketing;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactInquiryMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array{name: string, email: string, company: ?string, phone: ?string, message: string, intent: ?string}  $inquiry
     */
    public function __construct(public array $inquiry) {}

    public function envelope(): Envelope
    {
        $intent = $this->inquiry['intent'] ?? null;
        $subject = $intent === 'demo'
            ? 'Algos demo request from '.$this->inquiry['name']
            : 'Algos contact inquiry from '.$this->inquiry['name'];

        return new Envelope(
            subject: $subject,
            replyTo: [$this->inquiry['email']],
        );
    }

    public function content(): Content
    {
        return new Content(
            text: 'emails.marketing.contact-inquiry',
        );
    }
}
