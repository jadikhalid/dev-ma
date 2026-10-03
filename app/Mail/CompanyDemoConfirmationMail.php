<?php

namespace App\Mail;

use App\Models\CompanyDemoRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CompanyDemoConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public CompanyDemoRequest $demoRequest,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: MailSender::from(),
            subject: __('talenma.mail.company_demo_confirmation.subject'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.company-demo-confirmation',
            with: [
                'demo' => $this->demoRequest,
            ],
        );
    }
}
