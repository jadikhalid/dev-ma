<?php

namespace App\Mail;

use App\Models\PendingRegistration;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class VerifyRegistrationMail extends Mailable
{
    public function __construct(
        public PendingRegistration $pending,
        public string $verificationUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('talenma.mail.verify_registration.subject'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.verify-registration',
        );
    }
}
