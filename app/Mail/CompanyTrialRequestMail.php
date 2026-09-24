<?php

namespace App\Mail;

use App\Models\CompanyTrialRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CompanyTrialRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public CompanyTrialRequest $trialRequest,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: MailSender::from(),
            replyTo: [$this->trialRequest->email],
            subject: __('talenma.mail.company_trial_request.subject', [
                'company' => $this->trialRequest->company_name,
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.company-trial-request',
            with: [
                'trial' => $this->trialRequest,
            ],
        );
    }
}
