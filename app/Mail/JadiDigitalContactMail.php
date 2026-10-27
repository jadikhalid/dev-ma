<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class JadiDigitalContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $name,
        public string $email,
        public ?string $company,
        public string $body,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: MailSender::from(),
            replyTo: [new Address($this->email, $this->name)],
            subject: __('talenma.mail.jadi_digital_contact.subject', [
                'name' => $this->name,
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.jadi-digital-contact',
        );
    }
}
