<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ConfirmPendingEmailMail extends Mailable
{
    public function __construct(
        public User $user,
        public string $pendingEmail,
        public string $confirmUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('talenma.mail.confirm_pending_email.subject'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.confirm-pending-email',
        );
    }
}
