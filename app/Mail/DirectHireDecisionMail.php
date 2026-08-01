<?php

namespace App\Mail;

use App\Models\DirectHireRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DirectHireDecisionMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public DirectHireRequest $directHire,
        public string $decision,
    ) {}

    public function envelope(): Envelope
    {
        $talentName = $this->directHire->talentFormalDisplayName();

        return new Envelope(
            from: MailSender::from(),
            subject: __('talenma.mail.direct_hire_decision.subject_'.$this->decision, [
                'talent' => $talentName,
            ]),
        );
    }

    public function content(): Content
    {
        $url = $this->directHire->isStaffInternal()
            ? route('admin.direct-hire.show', $this->directHire)
            : route('company.direct-hire.show', $this->directHire);

        return new Content(
            view: 'emails.direct-hire-decision',
            with: [
                'directHire' => $this->directHire,
                'decision' => $this->decision,
                'greetingName' => $this->directHire->companyRecipientGreetingName(),
                'talentName' => $this->directHire->talentFormalDisplayName(),
                'url' => $url,
            ],
        );
    }
}
