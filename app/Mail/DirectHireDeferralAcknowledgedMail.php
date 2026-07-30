<?php

namespace App\Mail;

use App\Models\DirectHireRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DirectHireDeferralAcknowledgedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public DirectHireRequest $directHire) {}

    public function envelope(): Envelope
    {
        $companyName = $this->directHire->companyFormalDisplayName();

        return new Envelope(
            from: MailSender::from(),
            subject: __('talenma.mail.direct_hire_deferral_acknowledged.subject', [
                'company' => $companyName,
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.direct-hire-deferral-acknowledged',
            with: [
                'directHire' => $this->directHire,
                'talentName' => $this->directHire->talentFormalDisplayName(),
                'companyName' => $this->directHire->companyRecipientGreetingName(),
                'url' => route('talent.direct-hire.show', $this->directHire),
            ],
        );
    }
}
