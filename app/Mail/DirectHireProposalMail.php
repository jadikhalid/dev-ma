<?php

namespace App\Mail;

use App\Models\DirectHireRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DirectHireProposalMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public DirectHireRequest $directHire) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('talenma.mail.direct_hire_proposal.subject', [
                'company' => $this->directHire->companyDisplayName(),
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.direct-hire-proposal',
            with: [
                'directHire' => $this->directHire,
                'companyName' => $this->directHire->companyDisplayName(),
                'talent' => $this->directHire->talent,
                'url' => route('talent.direct-hire.show', $this->directHire),
            ],
        );
    }
}
