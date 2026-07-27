<?php

namespace App\Mail;

use App\Models\DirectHireRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DirectHireClosedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public DirectHireRequest $directHire) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('talenma.mail.direct_hire_closed.subject_'.$this->directHire->status, [
                'company' => $this->directHire->companyDisplayName(),
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.direct-hire-closed',
            with: [
                'directHire' => $this->directHire,
                'outcome' => $this->directHire->status,
                'talentName' => $this->directHire->talentDisplayName()
                    ?: ($this->directHire->talent?->name ?? ''),
                'companyName' => $this->directHire->companyDisplayName(),
                'url' => route('talent.direct-hire.show', $this->directHire),
            ],
        );
    }
}
