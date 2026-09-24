<?php

namespace App\Mail;

use App\Models\CompanyDemoRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CompanyDemoRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public CompanyDemoRequest $demoRequest,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: MailSender::from(),
            replyTo: [$this->demoRequest->email],
            subject: __('talenma.mail.company_demo_request.subject', [
                'company' => $this->demoRequest->company_name,
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.company-demo-request',
            with: [
                'demo' => $this->demoRequest,
            ],
        );
    }
}
