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
        return new Envelope(
            subject: __('talenma.mail.direct_hire_decision.subject_'.$this->decision, [
                'talent' => $this->directHire->talent?->name ?? '',
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.direct-hire-decision',
            with: [
                'directHire' => $this->directHire,
                'decision' => $this->decision,
                'company' => $this->directHire->company,
                'talentName' => $this->directHire->talent?->name ?? '',
                'url' => route('company.direct-hire.show', $this->directHire),
            ],
        );
    }
}
