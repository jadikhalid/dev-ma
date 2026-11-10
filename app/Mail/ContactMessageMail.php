<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $name,
        public string $email,
        public string $role,
        public string $subjectLine,
        public string $body,
        public string $audience,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: MailSender::from(),
            replyTo: [new Address($this->email, $this->name)],
            subject: __('talenma.mail.contact_message.subject', [
                'subject' => $this->subjectLine,
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-message',
        );
    }
}
